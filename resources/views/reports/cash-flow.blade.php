@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-doc report-page-wrap mx-auto">
        <div class="report-doc__body">
            <div class="report-doc__section">
                <h2 class="report-doc__section-title">Cash flows from operating activities</h2>
                <div class="report-table-wrap">
                    <div class="overflow-x-auto table-scroll-touch">
                        <table class="report-table">
                            <tbody>
                                @foreach ($report['operating'] as $row)
                                    <tr @class(['report-table__total' => $row['bold'] ?? false])>
                                        <td>{{ $row['label'] }}</td>
                                        <td @class(['text-right', 'text-rose-600 dark:text-rose-400' => ($row['amount'] ?? 0) < 0])>{{ $user->formatMoney($row['amount']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="report-doc__section">
                <h2 class="report-doc__section-title">Investing activities</h2>
                <div class="report-table-wrap">
                    <div class="overflow-x-auto table-scroll-touch">
                        <table class="report-table">
                            <tbody>
                                @foreach ($report['investing'] as $row)
                                    <tr @class(['report-table__total' => $row['bold'] ?? false])>
                                        <td>{{ $row['label'] }}</td>
                                        <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="report-doc__section">
                <h2 class="report-doc__section-title">Financing activities</h2>
                <div class="report-table-wrap">
                    <div class="overflow-x-auto table-scroll-touch">
                        <table class="report-table">
                            <tbody>
                                @foreach ($report['financing'] as $row)
                                    <tr @class(['report-table__total' => $row['bold'] ?? false])>
                                        <td>{{ $row['label'] }}</td>
                                        <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="report-highlight mb-4">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 sm:col-span-4">
                        <span class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Net change in cash</span>
                        <p class="mb-0 mt-1 text-sm font-bold tabular-nums">{{ $user->formatMoney($report['summary']['net_change']) }}</p>
                    </div>
                    <div class="col-span-12 sm:col-span-4">
                        <span class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Opening cash</span>
                        <p class="mb-0 mt-1 text-sm font-bold tabular-nums">{{ $user->formatMoney($report['summary']['opening_cash']) }}</p>
                    </div>
                    <div class="col-span-12 sm:col-span-4">
                        <span class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Closing cash</span>
                        <p class="mb-0 mt-1 text-sm font-bold tabular-nums">{{ $user->formatMoney($report['summary']['closing_cash']) }}</p>
                    </div>
                </div>
            </div>

            @if (count($report['monthly']) > 0)
                <div class="report-doc__section">
                    <h2 class="report-doc__section-title">Monthly cash movement</h2>
                    <div class="report-table-wrap">
                        <div class="overflow-x-auto table-scroll-touch">
                            <table class="report-table table-mobile-stack">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-right">Inflow</th>
                                        <th class="text-right">Outflow</th>
                                        <th class="text-right">Net</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report['monthly'] as $m)
                                        <tr>
                                            <td data-label="Month">{{ $m['month'] }}</td>
                                            <td class="text-right text-emerald-600 dark:text-emerald-400" data-label="Inflow">{{ $user->formatMoney($m['inflow']) }}</td>
                                            <td class="text-right text-rose-600 dark:text-rose-400" data-label="Outflow">{{ $user->formatMoney($m['outflow']) }}</td>
                                            <td class="text-right font-semibold" data-label="Net">{{ $user->formatMoney($m['net']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
