@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page card-panel bg-white mx-auto" style="max-width: 56rem;">
        <h2 class="h6 fw-semibold">Cash flows from operating activities</h2>
        <table class="table table-sm mb-4">
            @foreach ($report['operating'] as $row)
                <tr @class(['fw-bold' => $row['bold'] ?? false])>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-end {{ ($row['amount'] ?? 0) < 0 ? 'text-danger' : '' }}">{{ $user->formatMoney($row['amount']) }}</td>
                </tr>
            @endforeach
        </table>

        <h2 class="h6 fw-semibold">Investing activities</h2>
        <table class="table table-sm mb-4">
            @foreach ($report['investing'] as $row)
                <tr @class(['fw-bold' => $row['bold'] ?? false])>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-end">{{ $user->formatMoney($row['amount']) }}</td>
                </tr>
            @endforeach
        </table>

        <h2 class="h6 fw-semibold">Financing activities</h2>
        <table class="table table-sm mb-4">
            @foreach ($report['financing'] as $row)
                <tr @class(['fw-bold' => $row['bold'] ?? false])>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-end">{{ $user->formatMoney($row['amount']) }}</td>
                </tr>
            @endforeach
        </table>

        <div class="rounded-3 border p-4 bg-slate-50 dark:bg-slate-800 mb-4">
            <div class="row g-2">
                <div class="col-4"><span class="small text-secondary">Net change in cash</span><br><strong>{{ $user->formatMoney($report['summary']['net_change']) }}</strong></div>
                <div class="col-4"><span class="small text-secondary">Opening cash</span><br><strong>{{ $user->formatMoney($report['summary']['opening_cash']) }}</strong></div>
                <div class="col-4"><span class="small text-secondary">Closing cash</span><br><strong>{{ $user->formatMoney($report['summary']['closing_cash']) }}</strong></div>
            </div>
        </div>

        @if (count($report['monthly']) > 0)
            <h2 class="h6 fw-semibold">Monthly cash movement</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr><th>Month</th><th class="text-end">Inflow</th><th class="text-end">Outflow</th><th class="text-end">Net</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($report['monthly'] as $m)
                            <tr>
                                <td>{{ $m['month'] }}</td>
                                <td class="text-end text-success">{{ $user->formatMoney($m['inflow']) }}</td>
                                <td class="text-end text-danger">{{ $user->formatMoney($m['outflow']) }}</td>
                                <td class="text-end fw-semibold">{{ $user->formatMoney($m['net']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
