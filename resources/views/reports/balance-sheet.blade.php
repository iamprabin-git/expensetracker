@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-doc report-page-wrap mx-auto">
        <div class="report-doc__body">
            <p class="mb-4 text-sm text-muted-foreground">As of {{ $report['as_of'] }} · Based on cumulative transactions.</p>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-6">
                    <h2 class="report-doc__section-title">Assets</h2>
                    <div class="report-table-wrap">
                        <div class="overflow-x-auto table-scroll-touch">
                            <table class="report-table">
                                <tbody>
                                    @foreach ($report['assets'] as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="report-table__total">
                                        <td>Total assets</td>
                                        <td class="text-right">{{ $user->formatMoney($report['total_assets']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 md:col-span-6">
                    <h2 class="report-doc__section-title">Liabilities</h2>
                    <div class="report-table-wrap">
                        <div class="overflow-x-auto table-scroll-touch">
                            <table class="report-table">
                                <tbody>
                                    @foreach ($report['liabilities'] as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="report-table__total">
                                        <td>Total liabilities</td>
                                        <td class="text-right">{{ $user->formatMoney($report['total_liabilities']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h2 class="report-doc__section-title mt-4">Equity</h2>
                    <div class="report-table-wrap">
                        <div class="overflow-x-auto table-scroll-touch">
                            <table class="report-table">
                                <tbody>
                                    @foreach ($report['equity'] as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-right">{{ $user->formatMoney($row['amount']) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="report-table__total">
                                        <td>Total equity</td>
                                        <td class="text-right">{{ $user->formatMoney($report['total_equity']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 border-t border-border pt-3 text-sm font-semibold">Liabilities + Equity: {{ $user->formatMoney($report['total_liabilities_equity']) }}</p>
                </div>
            </div>
            @if ($report['balanced'])
                <p class="mb-0 mt-4 border-t border-border pt-3 text-sm text-emerald-600 dark:text-emerald-400">✓ Assets = Liabilities + Equity</p>
            @endif
        </div>
    </div>
@endsection
