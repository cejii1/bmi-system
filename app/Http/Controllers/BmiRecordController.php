<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BmiRecord;
use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BmiRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = BmiRecord::with('personnel');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('personnel', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('badge_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('bmi_category', $request->category);
        }

        if ($request->filled('station')) {
            $query->whereHas('personnel', function ($q) use ($request) {
                $q->where('station', $request->station);
            });
        }

        $records  = $query->orderByDesc('assessed_date')->paginate(15)->withQueryString();
        $stations = Personnel::distinct()->orderBy('station')->pluck('station');

        return view('bmi-records.index', compact('records', 'stations'));
    }

    public function create(Request $request)
    {
        $personnel     = null;
        $personnelList = Personnel::orderBy('last_name')->get();

        if ($request->filled('personnel_id')) {
            $personnel = Personnel::findOrFail($request->personnel_id);
        }

        return view('bmi-records.create', compact('personnel', 'personnelList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'personnel_id'      => 'required|exists:personnel,id',
            'height'            => 'required|numeric|min:50|max:250',
            'weight'            => 'required|numeric|min:10|max:300',
            'waist'             => 'nullable|numeric|min:30|max:200',
            'wrist'             => 'nullable|numeric|min:10|max:50',
            'hip'               => 'nullable|numeric|min:30|max:200',
            'assessed_date'     => 'required|date|date_equals:today',
            'bmi_value'         => 'required|numeric',
            'bmi_category'      => 'required|string',
            'weight_to_lose'    => 'nullable|numeric',
            'normal_weight_min' => 'required|numeric',
            'normal_weight_max' => 'required|numeric',
            'body_frame'        => 'nullable|string',
            'waist_hip_ratio'   => 'nullable|numeric',
        ]);

        // Convert cm to meters for storage
        $validated['height'] = $validated['height'] / 100;

        $personnel        = Personnel::findOrFail($validated['personnel_id']);
        $validated['age'] = $personnel->age;
        $validated['assessment_period'] = date('Y-m', strtotime($validated['assessed_date']));

        // One assessment per officer per month
        $exists = BmiRecord::where('personnel_id', $validated['personnel_id'])
            ->where('assessment_period', $validated['assessment_period'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'personnel_id' => 'This officer already has an assessment for ' . Carbon::parse($validated['assessed_date'])->format('F Y') . '.',
            ]);
        }

        $record = BmiRecord::create($validated);

        AuditLog::log('created', "Created BMI assessment for {$personnel->last_name}, {$personnel->first_name} (BMI: {$record->bmi_value})", $record);

        return redirect()->route('bmi-records.index')
            ->with('success', 'BMI assessment recorded successfully.');
    }

    public function show(BmiRecord $bmiRecord)
    {
        $bmiRecord->load('personnel');
        return view('bmi-records.show', compact('bmiRecord'));
    }

    public function edit(BmiRecord $bmiRecord)
    {
        $bmiRecord->load('personnel');
        return view('bmi-records.edit', compact('bmiRecord'));
    }

    public function update(Request $request, BmiRecord $bmiRecord)
    {
        $validated = $request->validate([
            'height'            => 'required|numeric|min:50|max:250',
            'weight'            => 'required|numeric|min:10|max:300',
            'waist'             => 'nullable|numeric|min:30|max:200',
            'wrist'             => 'nullable|numeric|min:10|max:50',
            'hip'               => 'nullable|numeric|min:30|max:200',
            'assessed_date'     => 'required|date',
            'bmi_value'         => 'required|numeric',
            'bmi_category'      => 'required|string',
            'weight_to_lose'    => 'nullable|numeric',
            'normal_weight_min' => 'required|numeric',
            'normal_weight_max' => 'required|numeric',
            'body_frame'        => 'nullable|string',
            'waist_hip_ratio'   => 'nullable|numeric',
        ]);

        // Convert cm to meters for storage
        $validated['height'] = $validated['height'] / 100;
        $validated['assessment_period'] = date('Y-m', strtotime($validated['assessed_date']));

        $oldValues = $bmiRecord->getOriginal();
        $bmiRecord->update($validated);

        AuditLog::log('updated', "Updated BMI record for {$bmiRecord->personnel->last_name}, {$bmiRecord->personnel->first_name}", $bmiRecord, $oldValues, $validated);

        return redirect()->route('bmi-records.show', $bmiRecord)
            ->with('success', 'BMI record updated successfully.');
    }

    public function destroy(BmiRecord $bmiRecord)
    {
        AuditLog::log('deleted', "Archived BMI record for {$bmiRecord->personnel->last_name}, {$bmiRecord->personnel->first_name}", $bmiRecord);

        $bmiRecord->delete();

        return redirect()->route('bmi-records.index')
            ->with('success', 'BMI record archived successfully.');
    }

    public function archived(Request $request)
    {
        $query = BmiRecord::onlyTrashed()->with('personnel');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('personnel', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('badge_number', 'like', "%{$search}%");
            });
        }

        $records = $query->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return view('bmi-records.archived', compact('records'));
    }

    public function restore($id)
    {
        $record = BmiRecord::onlyTrashed()->findOrFail($id);
        $record->restore();

        AuditLog::log('restored', "Restored BMI record #{$record->id}", $record);

        return redirect()->route('bmi-records.archived')
            ->with('success', 'BMI record restored successfully.');
    }
}
