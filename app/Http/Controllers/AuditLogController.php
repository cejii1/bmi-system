<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = AuditLog::distinct()->orderBy('action')->pluck('action');
        $models = AuditLog::distinct()->whereNotNull('model_type')->orderBy('model_type')->pluck('model_type');
        $users = AuditLog::distinct()->whereNotNull('user_name')->orderBy('user_name')->pluck('user_name', 'user_id');

        return view('audit-logs.index', compact('logs', 'actions', 'models', 'users'));
    }
}
