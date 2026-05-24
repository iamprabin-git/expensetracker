@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-doc report-page-wrap mx-auto">
        <div class="report-doc__body">
            <div class="report-doc__section">
                <h2 class="report-doc__section-title text-emerald-600 dark:text-emerald-400">Revenue</h2>
                <div class="report-table-wrap">
                    <div class="overflow-x-auto table-scroll-touch">
                        <table class="report-table">
                            <tbody>
                                @forelse ($report['revenue'] as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td class="text-right">{{ $user->formatMoney($row['total']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted-foreground">No income in period</td></tr>
                                @endforelse
                                <tr class="report-table__total">
                                    <td>Total revenue</td>
                                    <td class="text-right text-emerald-600 dark:text-emerald-400">{{ $user->formatMoney($report['total_revenue']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="report-doc__section">
                <h2 class="report-doc__section-title text-rose-600 dark:text-rose-400">Expenses</h2>
                <div class="report-table-wrap">
                    <div class="overflow-x-auto table-scroll-touch">
                        <table class="report-table">
                            <tbody>
                                @forelse ($report['expenses'] as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td class="text-right">{{ $user->formatMoney($row['total']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted-foreground">No expenses in period</td></tr>
                                @endforelse
                                <tr class="report-table__total">
                                    <td>Total expenses</td>
                                    <td class="text-right text-rose-600 dark:text-rose-400">{{ $user->formatMoney($report['total_expenses']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div @class([
                'report-highlight',
                'report-highlight--success' => $report['is_profit'],
                'report-highlight--danger' => ! $report['is_profit'],
            ])>
                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Net {{ $report['is_profit'] ? 'profit' : 'loss' }}</p>
                <p @class([
                    'mb-0 text-xl font-bold tabular-nums',
                    'text-emerald-600 dark:text-emerald-400' => $report['is_profit'],
                    'text-rose-600 dark:text-rose-400' => ! $report['is_profit'],
                ])>
                    {{ $user->formatMoney(abs($report['net_profit'])) }}
                </p>
            </div>
        </div>
    </div>
@endsection
