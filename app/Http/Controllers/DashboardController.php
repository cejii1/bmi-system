<?php

namespace App\Http\Controllers;

use App\Models\BmiRecord;
use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        }

        return $this->officerDashboard();
    }

    private function adminDashboard(Request $request)
    {
        $totalPersonnel = Personnel::count();

        // Determine selected period from filter (default: current month)
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedPeriod = sprintf('%04d-%02d', $selectedYear, $selectedMonth);
        $selectedDistrict = $request->input('district', '');
        $selectedStation = $request->input('station', '');

        $periodLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        // District-station mapping
        $districtStations = config('stations');
        $allDistricts = array_keys($districtStations);

        // Build a station-to-district lookup
        $stationToDistrict = [];
        foreach ($districtStations as $district => $stations) {
            foreach ($stations as $station) {
                $stationToDistrict[$station] = $district;
            }
        }

        // Available years for filter
        $earliestYear = BmiRecord::min('assessed_date');
        $earliestYear = $earliestYear ? Carbon::parse($earliestYear)->year : Carbon::now()->year;
        $availableYears = range($earliestYear, Carbon::now()->year);

        // All stations for dropdown (filtered by district if selected)
        $allStations = $selectedDistrict
            ? collect($districtStations[$selectedDistrict] ?? [])
            : Personnel::distinct()->orderBy('station')->pluck('station');

        // If station selected doesn't belong to selected district, clear it
        if ($selectedDistrict && $selectedStation && ($stationToDistrict[$selectedStation] ?? '') !== $selectedDistrict) {
            $selectedStation = '';
        }

        // Base query scoped to period (and optionally district/station)
        $periodQuery = BmiRecord::where('assessment_period', $selectedPeriod);
        if ($selectedStation) {
            $stationPersonnelIds = Personnel::where('station', $selectedStation)->pluck('id');
            $periodQuery->whereIn('personnel_id', $stationPersonnelIds);
        } elseif ($selectedDistrict) {
            $districtStationList = $districtStations[$selectedDistrict] ?? [];
            $stationPersonnelIds = Personnel::whereIn('station', $districtStationList)->pluck('id');
            $periodQuery->whereIn('personnel_id', $stationPersonnelIds);
        }

        // Assessment count
        $assessedCount = (clone $periodQuery)->distinct('personnel_id')->count('personnel_id');

        // BMI category counts
        $categoryCounts = (clone $periodQuery)
            ->selectRaw('bmi_category, COUNT(*) as count')
            ->groupBy('bmi_category')
            ->pluck('count', 'bmi_category');

        $normalCount = $categoryCounts->get('Normal', 0);
        $overweightCount = $categoryCounts->get('Overweight', 0);
        $obeseCount = ($categoryCounts->get('Obese I', 0)) + ($categoryCounts->get('Obese II', 0));
        $underweightCount = $categoryCounts->get('Underweight', 0);

        // Recent assessments
        $recentQuery = BmiRecord::with('personnel')->where('assessment_period', $selectedPeriod);
        if ($selectedStation) {
            $recentQuery->whereIn('personnel_id', $stationPersonnelIds);
        } elseif ($selectedDistrict) {
            $recentQuery->whereIn('personnel_id', $stationPersonnelIds);
        }
        $recentRecords = $recentQuery
            ->orderByDesc('assessed_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Per-station summary for table (scoped to district if selected)
        $stationSummaryQuery = Personnel::selectRaw('station, COUNT(*) as total')
            ->groupBy('station')
            ->orderBy('station');

        if ($selectedDistrict) {
            $districtStationList = $districtStations[$selectedDistrict] ?? [];
            $stationSummaryQuery->whereIn('station', $districtStationList);
        }

        $stationSummary = $stationSummaryQuery->get()
            ->map(function ($station) use ($selectedPeriod, $stationToDistrict) {
                $personnelIds = Personnel::where('station', $station->station)->pluck('id');
                $station->assessed = BmiRecord::whereIn('personnel_id', $personnelIds)
                    ->where('assessment_period', $selectedPeriod)
                    ->distinct('personnel_id')
                    ->count('personnel_id');
                $station->district = $stationToDistrict[$station->station] ?? '';
                return $station;
            })
            ->sortBy(['district', 'station'])
            ->values();

        // Personnel count for selected station/district (or all)
        if ($selectedStation) {
            $filteredPersonnel = Personnel::where('station', $selectedStation)->count();
        } elseif ($selectedDistrict) {
            $filteredPersonnel = Personnel::whereIn('station', $districtStations[$selectedDistrict] ?? [])->count();
        } else {
            $filteredPersonnel = $totalPersonnel;
        }

        // Monthly BMI trend (last 6 months) for line chart
        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->subMonths($i);
            $period = $date->format('Y-m');

            $trendQuery = BmiRecord::where('assessment_period', $period);
            if ($selectedStation) {
                $stIds = Personnel::where('station', $selectedStation)->pluck('id');
                $trendQuery->whereIn('personnel_id', $stIds);
            } elseif ($selectedDistrict) {
                $stIds = Personnel::whereIn('station', $districtStations[$selectedDistrict] ?? [])->pluck('id');
                $trendQuery->whereIn('personnel_id', $stIds);
            }

            $periodCounts = $trendQuery->selectRaw("
                SUM(CASE WHEN bmi_category = 'Underweight' THEN 1 ELSE 0 END) as underweight,
                SUM(CASE WHEN bmi_category = 'Normal' THEN 1 ELSE 0 END) as normal,
                SUM(CASE WHEN bmi_category = 'Overweight' THEN 1 ELSE 0 END) as overweight,
                SUM(CASE WHEN bmi_category LIKE 'Obese%' THEN 1 ELSE 0 END) as obese,
                COUNT(*) as total
            ")->first();

            $monthlyTrend->push([
                'label' => $date->format('M Y'),
                'underweight' => (int) ($periodCounts->underweight ?? 0),
                'normal' => (int) ($periodCounts->normal ?? 0),
                'overweight' => (int) ($periodCounts->overweight ?? 0),
                'obese' => (int) ($periodCounts->obese ?? 0),
                'total' => (int) ($periodCounts->total ?? 0),
            ]);
        }

        // Station comparison data for bar chart (top 10 stations by assessed count)
        $stationComparison = $stationSummary
            ->where('assessed', '>', 0)
            ->sortByDesc('assessed')
            ->take(10)
            ->values()
            ->map(function ($station) use ($selectedPeriod) {
                $personnelIds = Personnel::where('station', $station->station)->pluck('id');
                $cats = BmiRecord::whereIn('personnel_id', $personnelIds)
                    ->where('assessment_period', $selectedPeriod)
                    ->selectRaw("
                        SUM(CASE WHEN bmi_category = 'Underweight' THEN 1 ELSE 0 END) as underweight,
                        SUM(CASE WHEN bmi_category = 'Normal' THEN 1 ELSE 0 END) as normal,
                        SUM(CASE WHEN bmi_category = 'Overweight' THEN 1 ELSE 0 END) as overweight,
                        SUM(CASE WHEN bmi_category LIKE 'Obese%' THEN 1 ELSE 0 END) as obese
                    ")->first();

                return [
                    'station' => str_replace([' MPS', ' PS'], '', $station->station),
                    'underweight' => (int) ($cats->underweight ?? 0),
                    'normal' => (int) ($cats->normal ?? 0),
                    'overweight' => (int) ($cats->overweight ?? 0),
                    'obese' => (int) ($cats->obese ?? 0),
                ];
            });

        return view('dashboard', compact(
            'totalPersonnel',
            'filteredPersonnel',
            'normalCount',
            'overweightCount',
            'obeseCount',
            'underweightCount',
            'recentRecords',
            'stationSummary',
            'periodLabel',
            'assessedCount',
            'selectedMonth',
            'selectedYear',
            'selectedDistrict',
            'selectedStation',
            'availableYears',
            'allDistricts',
            'allStations',
            'monthlyTrend',
            'stationComparison'
        ));
    }

    private function officerDashboard()
    {
        $user = auth()->user();
        $personnel = $user->personnel;

        $latestRecord = BmiRecord::where('personnel_id', $personnel->id)
            ->orderByDesc('assessed_date')
            ->first();

        $totalAssessments = BmiRecord::where('personnel_id', $personnel->id)->count();

        $recentRecords = BmiRecord::where('personnel_id', $personnel->id)
            ->orderByDesc('assessed_date')
            ->limit(10)
            ->get();

        // BMI trend data for chart (last 12 records)
        $trendRecords = BmiRecord::where('personnel_id', $personnel->id)
            ->orderBy('assessed_date')
            ->limit(12)
            ->get();

        return view('dashboard', compact(
            'personnel',
            'latestRecord',
            'totalAssessments',
            'recentRecords',
            'trendRecords'
        ));
    }
}
