@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page report-page-wrap card-panel bg-white mx-auto">
        <h2 class="h6 font-semibold">Cash flows from operating activities</h2>
        <div class="overflow-x-auto table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @foreach ($report['operating'] as $row)
                        <tr @class(['font-bold' => $row['bold'] ?? false])>
                            <td>{{ $row['label'] }}</td>
                            <td class="text-right {{ ($row['amount'] ?? 0) < 0 ? 'text-destructive' : '' }}">{{ $user->formatMoney($row['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2 class="h6 font-semibold">Investing activities</h2>
        <div class="overflow-x-auto table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @foreach ($report['investing'] as $row)
                        <tr @class(['font-bold' => $row['bold'] ?? false])>
                            <td>{{ $row['label'] }}</td>
                            <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2 class="h6 font-semibold">Financing activities</h2>
        <div class="overflow-x-auto table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @foreach ($report['financing'] as $row)
                        <tr @class(['font-bold' => $row['bold'] ?? false])>
                            <td>{{ $row['label'] }}</td>
                            <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rounded-3 border p-4 bg-slate-50 dark:bg-slate-800 mb-4">
            <div class="grid grid-cols-12 gap-4 g-2">
                <div class="col-span-12 col-sm-4"><span class="text-sm text-muted-foreground">Net change in cash</span><br><strong>{{ $user->formatMoney($report['summary']['net_change']) }}</strong></div>
                <div class="col-span-12 col-sm-4"><span class="text-sm text-muted-foreground">Opening cash</span><br><strong>{{ $user->formatMoney($report['summary']['opening_cash']) }}</strong></div>
                <div class="col-span-12 col-sm-4"><span class="text-sm text-muted-foreground">Closing cash</span><br><strong>{{ $user->formatMoney($report['summary']['closing_cash']) }}</strong></div>
            </div>
        </div>

        @if (count($report['monthly']) > 0)
            <h2 class="h6 font-semibold">Monthly cash movement</h2>
            <div class="overflow-x-auto table-scroll-touch">
                <table class="table table-bordered table-sm table-mobile-stack">
                    <thead class="table-light">
                        <tr><th>Month</th><th class="text-right">Inflow</th><th class="text-right">Outflow</th><th class="text-right">Net</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($report['monthly'] as $m)
                            <tr>
                                <td data-label="Month">{{ $m['month'] }}</td>
                                <td class="text-right text-success" data-label="Inflow">{{ $user->formatMoney($m['inflow']) }}</td>
                                <td class="text-right text-destructive" data-label="Outflow">{{ $user->formatMoney($m['outflow']) }}</td>
                                <td class="text-right font-semibold" data-label="Net">{{ $user->formatMoney($m['net']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
