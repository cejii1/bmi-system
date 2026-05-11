<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BmiRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class SelfAssessmentController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $personnel = $user->personnel;

        if (!$personnel) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account is not linked to a personnel record. Please contact an administrator.');
        }

        $currentPeriod = date('Y-m');
        $existingRecord = BmiRecord::where('personnel_id', $personnel->id)
            ->where('assessment_period', $currentPeriod)
            ->first();

        return view('self-assessment.create', compact('personnel', 'existingRecord'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $personnel = $user->personnel;

        if (!$personnel) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account is not linked to a personnel record.');
        }

        $validated = $request->validate([
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
            'photo_front'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'photo_right'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'photo_left'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $validated['height'] = $validated['height'] / 100;
        $validated['personnel_id'] = $personnel->id;
        $validated['age'] = $personnel->age;
        $validated['assessment_period'] = date('Y-m', strtotime($validated['assessed_date']));

        $exists = BmiRecord::where('personnel_id', $personnel->id)
            ->where('assessment_period', $validated['assessment_period'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'assessed_date' => 'You already have an assessment for ' . Carbon::parse($validated['assessed_date'])->format('F Y') . '.',
            ]);
        }

        // Handle photo uploads
        foreach (['photo_front', 'photo_right', 'photo_left'] as $photoField) {
            if ($request->hasFile($photoField)) {
                $validated[$photoField] = $this->compressAndStorePhoto($request->file($photoField), $personnel->id);
            } else {
                unset($validated[$photoField]);
            }
        }

        $record = BmiRecord::create($validated);

        AuditLog::log('created', "Self-assessed BMI for {$personnel->last_name}, {$personnel->first_name} (BMI: {$record->bmi_value})", $record);

        return redirect()->route('self-assessment.create')
            ->with('success', 'Your BMI assessment has been recorded successfully.');
    }

    public function edit()
    {
        $user = auth()->user();
        $personnel = $user->personnel;

        if (!$personnel) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account is not linked to a personnel record.');
        }

        $currentPeriod = date('Y-m');
        $record = BmiRecord::where('personnel_id', $personnel->id)
            ->where('assessment_period', $currentPeriod)
            ->firstOrFail();

        return view('self-assessment.edit', compact('personnel', 'record'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $personnel = $user->personnel;

        if (!$personnel) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account is not linked to a personnel record.');
        }

        $currentPeriod = date('Y-m');
        $record = BmiRecord::where('personnel_id', $personnel->id)
            ->where('assessment_period', $currentPeriod)
            ->firstOrFail();

        $validated = $request->validate([
            'height'            => 'required|numeric|min:50|max:250',
            'weight'            => 'required|numeric|min:10|max:300',
            'waist'             => 'nullable|numeric|min:30|max:200',
            'wrist'             => 'nullable|numeric|min:10|max:50',
            'hip'               => 'nullable|numeric|min:30|max:200',
            'bmi_value'         => 'required|numeric',
            'bmi_category'      => 'required|string',
            'weight_to_lose'    => 'nullable|numeric',
            'normal_weight_min' => 'required|numeric',
            'normal_weight_max' => 'required|numeric',
            'body_frame'        => 'nullable|string',
            'waist_hip_ratio'   => 'nullable|numeric',
            'photo_front'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'photo_right'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'photo_left'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $validated['height'] = $validated['height'] / 100;

        // Handle photo uploads
        foreach (['photo_front', 'photo_right', 'photo_left'] as $photoField) {
            if ($request->hasFile($photoField)) {
                // Delete old photo
                if ($record->$photoField) {
                    Storage::disk('public')->delete($record->$photoField);
                }
                $validated[$photoField] = $this->compressAndStorePhoto($request->file($photoField), $personnel->id);
            } else {
                unset($validated[$photoField]);
            }
        }

        $oldValues = $record->getOriginal();
        $record->update($validated);

        AuditLog::log('updated', "Updated self-assessment for {$personnel->last_name}, {$personnel->first_name}", $record, $oldValues, $validated);

        return redirect()->route('self-assessment.create')
            ->with('success', 'Your BMI assessment has been updated successfully.');
    }

    private function compressAndStorePhoto($file, $personnelId): string
    {
        $filename = 'body-photos/' . $personnelId . '_' . time() . '_' . uniqid() . '.jpg';
        $path = storage_path('app/public/' . $filename);

        Storage::disk('public')->makeDirectory('body-photos');

        // Resize to max 800px height, keep aspect ratio, compress to 70% JPEG
        Image::read($file->getPathname())
            ->scaleDown(height: 800)
            ->toJpeg(70)
            ->save($path);

        return $filename;
    }
}
