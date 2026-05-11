<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BMI Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }

        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; }
        .header h1 { font-size: 14px; color: #1e3a5f; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .header h2 { font-size: 11px; color: #555; margin-bottom: 2px; }
        .header p { font-size: 9px; color: #888; }
        .header .logos { margin-bottom: 5px; }

        .filters { background: #f8f9fa; padding: 6px 10px; margin-bottom: 12px; border-radius: 4px; font-size: 9px; color: #666; }
        .filters strong { color: #333; }

        .summary-grid { display: table; width: 100%; margin-bottom: 12px; }
        .summary-cell { display: table-cell; text-align: center; padding: 8px; border: 1px solid #e5e7eb; }
        .summary-cell .label { font-size: 8px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
        .summary-cell .value { font-size: 16px; font-weight: bold; margin: 2px 0; }
        .summary-cell .pct { font-size: 8px; color: #888; }

        .section-title { font-size: 11px; font-weight: bold; color: #1e3a5f; margin: 12px 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #1e3a5f; color: #fff; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; padding: 5px 4px; text-align: left; }
        td { padding: 4px; font-size: 9px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #f9fafb; }

        .district-row { background: #e8ecf1 !important; }
        .district-row td { font-weight: bold; font-size: 9px; color: #1e3a5f; padding: 4px 4px; text-transform: uppercase; letter-spacing: 0.5px; }

        .totals-row { background: #f0f0f0 !important; font-weight: bold; }
        .totals-row td { border-top: 2px solid #1e3a5f; padding: 5px 4px; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .cat-underweight { color: #2563eb; }
        .cat-normal { color: #16a34a; }
        .cat-overweight { color: #ca8a04; }
        .cat-obese { color: #dc2626; }

        .footer { text-align: center; font-size: 8px; color: #aaa; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 5px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Philippine National Police</h1>
        <h2>La Union Police Provincial Office (LUPPO)</h2>
        <h1 style="margin-top: 6px; font-size: 13px;">BMI Monitoring Report</h1>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <!-- Active Filters -->
    <div class="filters">
        <strong>Filters:</strong>
        Year: {{ $selectedYear ?: 'All' }} |
        Period: {{ $selectedPeriod ?: 'All' }} |
        District: {{ $selectedDistrict ?: 'All' }} |
        Station: {{ $selectedStation ?: 'All' }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Records: {{ $totalRecords }}</strong>
    </div>

    <!-- Summary -->
    <table style="margin-bottom: 12px;">
        <tr>
            <td style="text-align: center; padding: 8px; border: 1px solid #ddd; width: 20%;">
                <div style="font-size: 8px; text-transform: uppercase; color: #888;">Total</div>
                <div style="font-size: 18px; font-weight: bold; color: #333;">{{ $totalRecords }}</div>
            </td>
            <td style="text-align: center; padding: 8px; border: 1px solid #ddd; border-left: 3px solid #2563eb; width: 20%;">
                <div style="font-size: 8px; text-transform: uppercase; color: #888;">Underweight</div>
                <div style="font-size: 18px; font-weight: bold; color: #2563eb;">{{ $categoryCounts['Underweight'] }}</div>
                <div style="font-size: 8px; color: #888;">{{ $totalRecords ? number_format($categoryCounts['Underweight'] / $totalRecords * 100, 1) : 0 }}%</div>
            </td>
            <td style="text-align: center; padding: 8px; border: 1px solid #ddd; border-left: 3px solid #16a34a; width: 20%;">
                <div style="font-size: 8px; text-transform: uppercase; color: #888;">Normal</div>
                <div style="font-size: 18px; font-weight: bold; color: #16a34a;">{{ $categoryCounts['Normal'] }}</div>
                <div style="font-size: 8px; color: #888;">{{ $totalRecords ? number_format($categoryCounts['Normal'] / $totalRecords * 100, 1) : 0 }}%</div>
            </td>
            <td style="text-align: center; padding: 8px; border: 1px solid #ddd; border-left: 3px solid #ca8a04; width: 20%;">
                <div style="font-size: 8px; text-transform: uppercase; color: #888;">Overweight</div>
                <div style="font-size: 18px; font-weight: bold; color: #ca8a04;">{{ $categoryCounts['Overweight'] }}</div>
                <div style="font-size: 8px; color: #888;">{{ $totalRecords ? number_format($categoryCounts['Overweight'] / $totalRecords * 100, 1) : 0 }}%</div>
            </td>
            <td style="text-align: center; padding: 8px; border: 1px solid #ddd; border-left: 3px solid #dc2626; width: 20%;">
                <div style="font-size: 8px; text-transform: uppercase; color: #888;">Obese</div>
                <div style="font-size: 18px; font-weight: bold; color: #dc2626;">{{ $categoryCounts['Obese'] }}</div>
                <div style="font-size: 8px; color: #888;">{{ $totalRecords ? number_format($categoryCounts['Obese'] / $totalRecords * 100, 1) : 0 }}%</div>
            </td>
        </tr>
    </table>

    <!-- Station Summary -->
    @if(!empty($stationSummary))
        <div class="section-title">Station Summary</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Station</th>
                    <th class="text-center" style="width: 12%;">Total</th>
                    <th class="text-center" style="width: 12%;">Underweight</th>
                    <th class="text-center" style="width: 12%;">Normal</th>
                    <th class="text-center" style="width: 12%;">Overweight</th>
                    <th class="text-center" style="width: 12%;">Obese</th>
                </tr>
            </thead>
            <tbody>
                @php $currentDistrict = ''; @endphp
                @foreach($stationSummary as $row)
                    @if($row['district'] !== $currentDistrict)
                        @php $currentDistrict = $row['district']; @endphp
                        <tr class="district-row">
                            <td colspan="6">{{ $currentDistrict }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding-left: 15px;">{{ $row['station'] }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $row['total'] }}</td>
                        <td class="text-center cat-underweight">{{ $row['underweight'] ?: '—' }}</td>
                        <td class="text-center cat-normal">{{ $row['normal'] ?: '—' }}</td>
                        <td class="text-center cat-overweight">{{ $row['overweight'] ?: '—' }}</td>
                        <td class="text-center cat-obese">{{ $row['obese'] ?: '—' }}</td>
                    </tr>
                @endforeach
                <tr class="totals-row">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $totalRecords }}</td>
                    <td class="text-center cat-underweight">{{ $categoryCounts['Underweight'] }}</td>
                    <td class="text-center cat-normal">{{ $categoryCounts['Normal'] }}</td>
                    <td class="text-center cat-overweight">{{ $categoryCounts['Overweight'] }}</td>
                    <td class="text-center cat-obese">{{ $categoryCounts['Obese'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- Personnel Details -->
    @if($personnelRecords->isNotEmpty())
        <div class="page-break"></div>

        <div class="header">
            <h1>Philippine National Police</h1>
            <h2>La Union Police Provincial Office (LUPPO)</h2>
            <h1 style="margin-top: 6px; font-size: 13px;">BMI Monitoring Report — Personnel Details</h1>
        </div>

        <div class="section-title">Personnel BMI Details ({{ $totalRecords }} records)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 10%;">Rank/Title</th>
                    <th style="width: 5%;">Gender</th>
                    <th style="width: 12%;">Station</th>
                    <th style="width: 9%;">Date</th>
                    <th style="width: 6%;">Period</th>
                    <th style="width: 6%;">Height</th>
                    <th style="width: 6%;">Weight</th>
                    <th style="width: 5%;">BMI</th>
                    <th style="width: 9%;">Category</th>
                    <th style="width: 7%;">Body Frame</th>
                    <th style="width: 7%;">Wt. to Lose</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personnelRecords as $record)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $record->personnel->last_name }}, {{ $record->personnel->first_name }}</td>
                        <td>{{ $record->personnel->rank ?: $record->personnel->position_title ?: '—' }}</td>
                        <td class="text-center">{{ substr($record->personnel->gender, 0, 1) }}</td>
                        <td>{{ $record->personnel->station }}</td>
                        <td>{{ $record->assessed_date->format('m/d/Y') }}</td>
                        <td>{{ $record->assessment_period ? str_replace(' Semester', '', $record->assessment_period) : '—' }}</td>
                        <td class="text-center">{{ $record->height }}</td>
                        <td class="text-center">{{ $record->weight }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ number_format($record->bmi_value, 2) }}</td>
                        <td>
                            @if($record->bmi_value < 18.5)
                                <span class="cat-underweight">{{ $record->bmi_category }}</span>
                            @elseif($record->bmi_value < 25)
                                <span class="cat-normal">{{ $record->bmi_category }}</span>
                            @elseif($record->bmi_value < 30)
                                <span class="cat-overweight">{{ $record->bmi_category }}</span>
                            @else
                                <span class="cat-obese">{{ $record->bmi_category }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $record->body_frame ?? '—' }}</td>
                        <td class="text-center">{{ $record->weight_to_lose ? number_format($record->weight_to_lose, 1) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        BMI Monitoring System — La Union Police Provincial Office (LUPPO) — Report generated {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
