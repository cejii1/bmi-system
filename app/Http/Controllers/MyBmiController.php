<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyBmiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $personnel = $user->personnel;

        if (!$personnel) {
            return view('my-bmi.index', ['personnel' => null, 'records' => collect(), 'latestRecord' => null]);
        }

        $query = $personnel->bmiRecords();

        if ($request->filled('year')) {
            $query->whereYear('assessed_date', $request->year);
        }

        if ($request->filled('period')) {
            $query->where('assessment_period', $request->period);
        }

        $records = $query->orderBy('assessed_date', 'desc')->get();
        $latestRecord = $records->first();

        $years = $personnel->bmiRecords()
            ->selectRaw('YEAR(assessed_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('my-bmi.index', compact('personnel', 'records', 'latestRecord', 'years'));
    }
}
