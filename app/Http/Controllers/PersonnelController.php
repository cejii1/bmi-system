<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $query = Personnel::with(['latestBmiRecord', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('badge_number', 'like', "%{$search}%")
                  ->orWhere('rank', 'like', "%{$search}%")
                  ->orWhere('position_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        if ($request->filled('station')) {
            $query->where('station', $request->station);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $personnel = $query->orderBy('last_name')->paginate(15)->withQueryString();

        $units    = Personnel::distinct()->orderBy('unit')->pluck('unit');
        $stations = Personnel::distinct()->orderBy('station')->pluck('station');

        return view('personnel.index', compact('personnel', 'units', 'stations'));
    }

    public function create()
    {
        return view('personnel.create');
    }

    public function store(Request $request)
    {
        $isUniformed = $request->input('personnel_type') === 'Uniformed';

        $validated = $request->validate([
            'personnel_type' => 'required|in:Uniformed,Non-Uniformed',
            'rank'           => $isUniformed ? 'required|string|max:100' : 'nullable',
            'badge_number'   => $isUniformed ? 'required|string|max:50|unique:personnel,badge_number' : 'nullable',
            'position_title' => !$isUniformed ? 'required|string|max:150' : 'nullable',
            'last_name'      => 'required|string|max:100',
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'unit'           => 'required|string|max:100',
            'station'        => 'required|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'age'            => 'required|integer|min:1|max:100',
        ]);

        // Clear fields not applicable to the type
        if ($isUniformed) {
            $validated['position_title'] = null;
        } else {
            $validated['rank'] = null;
            $validated['badge_number'] = null;
        }

        Personnel::create($validated);

        return redirect()->route('personnel.index')
            ->with('success', 'Personnel record created successfully.');
    }

    public function show(Personnel $personnel)
    {
        $personnel->load(['bmiRecords', 'user']);
        return view('personnel.show', compact('personnel'));
    }

    public function edit(Personnel $personnel)
    {
        return view('personnel.edit', compact('personnel'));
    }

    public function update(Request $request, Personnel $personnel)
    {
        $isUniformed = $request->input('personnel_type') === 'Uniformed';

        $validated = $request->validate([
            'personnel_type' => 'required|in:Uniformed,Non-Uniformed',
            'rank'           => $isUniformed ? 'required|string|max:100' : 'nullable',
            'badge_number'   => $isUniformed ? 'required|string|max:50|unique:personnel,badge_number,' . $personnel->id : 'nullable',
            'position_title' => !$isUniformed ? 'required|string|max:150' : 'nullable',
            'last_name'      => 'required|string|max:100',
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'unit'           => 'required|string|max:100',
            'station'        => 'required|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'age'            => 'required|integer|min:1|max:100',
        ]);

        if ($isUniformed) {
            $validated['position_title'] = null;
        } else {
            $validated['rank'] = null;
            $validated['badge_number'] = null;
        }

        $oldValues = $personnel->getOriginal();
        $personnel->update($validated);

        AuditLog::log('updated', "Updated personnel record for {$personnel->last_name}, {$personnel->first_name}", $personnel, $oldValues, $validated);

        return redirect()->route('personnel.show', $personnel)
            ->with('success', 'Personnel record updated successfully.');
    }

    public function destroy(Personnel $personnel)
    {
        AuditLog::log('deleted', "Archived personnel record for {$personnel->last_name}, {$personnel->first_name}", $personnel);

        $personnel->delete();

        return redirect()->route('personnel.index')
            ->with('success', 'Personnel record archived successfully.');
    }

    public function archived(Request $request)
    {
        $query = Personnel::onlyTrashed()->with('latestBmiRecord');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('badge_number', 'like', "%{$search}%")
                  ->orWhere('rank', 'like', "%{$search}%")
                  ->orWhere('position_title', 'like', "%{$search}%");
            });
        }

        $personnel = $query->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();

        return view('personnel.archived', compact('personnel'));
    }

    public function restore($id)
    {
        $personnel = Personnel::onlyTrashed()->findOrFail($id);
        $personnel->restore();

        AuditLog::log('restored', "Restored personnel record for {$personnel->last_name}, {$personnel->first_name}", $personnel);

        return redirect()->route('personnel.archived')
            ->with('success', 'Personnel record restored successfully.');
    }

    public function forceDelete($id)
    {
        $personnel = Personnel::onlyTrashed()->findOrFail($id);

        // Delete associated user account
        \App\Models\User::where('personnel_id', $personnel->id)->delete();

        // Delete associated BMI records
        \App\Models\BmiRecord::where('personnel_id', $personnel->id)->forceDelete();

        AuditLog::log('force_deleted', "Permanently deleted personnel: {$personnel->last_name}, {$personnel->first_name}", $personnel);

        // Permanently delete personnel
        $personnel->forceDelete();

        return redirect()->route('personnel.archived')
            ->with('success', 'Personnel record and associated data permanently deleted.');
    }
}
