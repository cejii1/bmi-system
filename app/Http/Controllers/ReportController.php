<?php

namespace App\Http\Controllers;

use App\Models\BmiRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $data['isPdf'] = true;

        $pdf = Pdf::loadView('reports.pdf', $data)
            ->setPaper('legal', 'landscape');

        $filename = 'BMI_Report_' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);
        $records = $data['personnelRecords'];

        $filename = 'BMI_Report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Last Name', 'First Name', 'Middle Name', 'Personnel Type',
                'Rank', 'Badge No.', 'Position Title', 'Gender', 'Age',
                'Unit', 'Station', 'District',
                'Assessment Date', 'Period', 'Height (m)', 'Weight (kg)',
                'BMI Value', 'BMI Category', 'Body Frame',
                'Normal Weight Min', 'Normal Weight Max', 'Weight to Lose (kg)',
            ]);

            foreach ($records as $record) {
                $p = $record->personnel;
                fputcsv($file, [
                    $p->last_name,
                    $p->first_name,
                    $p->middle_name ?? '',
                    $p->personnel_type,
                    $p->rank ?? '',
                    $p->badge_number ?? '',
                    $p->position_title ?? '',
                    $p->gender,
                    $record->age,
                    $p->unit,
                    $p->station,
                    $record->district ?? '',
                    $record->assessed_date->format('Y-m-d'),
                    $record->assessment_period ?? '',
                    $record->height,
                    $record->weight,
                    number_format($record->bmi_value, 2),
                    $record->bmi_category,
                    $record->body_frame ?? '',
                    $record->normal_weight_min ? number_format($record->normal_weight_min, 1) : '',
                    $record->normal_weight_max ? number_format($record->normal_weight_max, 1) : '',
                    $record->weight_to_lose ? number_format($record->weight_to_lose, 2) : '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getReportData(Request $request): array
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $districtStations = config('stations');
        $stationToDistrict = [];
        foreach ($districtStations as $district => $stations) {
            foreach ($stations as $station) {
                $stationToDistrict[$station] = $district;
            }
        }

        $selectedYear = $request->input('year');
        $selectedMonth = $request->input('month');
        $selectedPeriod = $request->input('period');
        $selectedCategory = $request->input('category');
        $selectedDistrict = $isAdmin ? $request->input('district') : null;
        $selectedStation = $isAdmin ? $request->input('station') : null;

        // Officer: auto-scope to their station
        $officerStation = null;
        if (!$isAdmin && $user->personnel) {
            $officerStation = $user->personnel->station;
        }

        // Build query for BMI records with personnel
        $query = BmiRecord::with('personnel')
            ->whereHas('personnel');

        if ($selectedYear) {
            $query->whereYear('assessed_date', $selectedYear);
        }
        if ($selectedMonth) {
            $query->whereMonth('assessed_date', $selectedMonth);
        }
        if ($selectedPeriod) {
            $query->where('assessment_period', $selectedPeriod);
        }
        if ($selectedCategory) {
            $query->where('bmi_category', $selectedCategory);
        }

        if (!$isAdmin && $officerStation) {
            // Officers only see their own station
            $query->whereHas('personnel', fn($q) => $q->where('station', $officerStation));
        } elseif ($selectedStation) {
            $query->whereHas('personnel', fn($q) => $q->where('station', $selectedStation));
        } elseif ($selectedDistrict && isset($districtStations[$selectedDistrict])) {
            $districtStationList = $districtStations[$selectedDistrict];
            $query->whereHas('personnel', fn($q) => $q->whereIn('station', $districtStationList));
        }

        $personnelRecords = $query->orderBy('assessed_date', 'desc')->get();

        // Add district info to each record
        foreach ($personnelRecords as $record) {
            $record->district = $stationToDistrict[$record->personnel->station] ?? 'Unknown';
        }

        // Category counts
        $categoryCounts = [
            'Underweight' => 0,
            'Normal' => 0,
            'Overweight' => 0,
            'Obese' => 0,
        ];
        foreach ($personnelRecords as $record) {
            if ($record->bmi_value < 18.5) {
                $categoryCounts['Underweight']++;
            } elseif ($record->bmi_value < 25) {
                $categoryCounts['Normal']++;
            } elseif ($record->bmi_value < 30) {
                $categoryCounts['Overweight']++;
            } else {
                $categoryCounts['Obese']++;
            }
        }
        $totalRecords = $personnelRecords->count();

        // Station summary
        $stationSummary = [];
        foreach ($personnelRecords as $record) {
            $station = $record->personnel->station;
            if (!isset($stationSummary[$station])) {
                $stationSummary[$station] = [
                    'station' => $station,
                    'district' => $stationToDistrict[$station] ?? 'Unknown',
                    'total' => 0,
                    'underweight' => 0,
                    'normal' => 0,
                    'overweight' => 0,
                    'obese' => 0,
                ];
            }
            $stationSummary[$station]['total']++;
            if ($record->bmi_value < 18.5) {
                $stationSummary[$station]['underweight']++;
            } elseif ($record->bmi_value < 25) {
                $stationSummary[$station]['normal']++;
            } elseif ($record->bmi_value < 30) {
                $stationSummary[$station]['overweight']++;
            } else {
                $stationSummary[$station]['obese']++;
            }
        }

        // Sort by district then station
        usort($stationSummary, function ($a, $b) {
            return [$a['district'], $a['station']] <=> [$b['district'], $b['station']];
        });

        // Available years
        $years = BmiRecord::selectRaw('YEAR(assessed_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return compact(
            'personnelRecords', 'categoryCounts', 'totalRecords',
            'stationSummary', 'years',
            'districtStations', 'stationToDistrict',
            'selectedYear', 'selectedMonth', 'selectedPeriod', 'selectedCategory',
            'selectedDistrict', 'selectedStation',
            'isAdmin', 'officerStation'
        );
    }
}
