<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AccountApprovalController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = User::where('role', 'officer')->with('personnel');

        if ($filter === 'pending') {
            $query->where('is_approved', false);
        } elseif ($filter === 'approved') {
            $query->where('is_approved', true);
        }

        $users = $query->latest()->paginate(20)->appends($request->query());

        $pendingCount = User::where('role', 'officer')->where('is_approved', false)->count();

        return view('account-approval.index', compact('users', 'filter', 'pendingCount'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);

        AuditLog::log('approved', "Approved account for {$user->name}", $user);

        return back()->with('success', "Account for {$user->name} has been approved.");
    }

    public function reject(User $user)
    {
        $personnel = $user->personnel;
        $name = $user->name;

        AuditLog::log('rejected', "Rejected and removed account for {$name}", $user);

        $user->delete();

        if ($personnel) {
            $personnel->delete();
        }

        return back()->with('success', "Account for {$name} has been rejected and removed.");
    }
}
