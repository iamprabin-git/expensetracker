@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page report-page-wrap card-panel bg-white mx-auto">
        <h2 class="h6 font-semibold text-success">Revenue</h2>
        <div class="overflow-x-auto table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @forelse ($report['revenue'] as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-right">{{ $user->formatMoney($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-muted-foreground">No income in period</td></tr>
                    @endforelse
                    <tr class="font-bold border-top">
                        <td>Total revenue</td>
                        <td class="text-right text-success">{{ $user->formatMoney($report['total_revenue']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="h6 font-semibold text-destructive">Expenses</h2>
        <div class="overflow-x-auto table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @forelse ($report['expenses'] as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-right">{{ $user->formatMoney($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-muted-foreground">No expenses in period</td></tr>
                    @endforelse
                    <tr class="font-bold border-top">
                        <td>Total expenses</td>
                        <td class="text-right text-destructive">{{ $user->formatMoney($report['total_expenses']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rounded-3 border p-4 {{ $report['is_profit'] ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
            <p class="mb-1 small text-muted-foreground">Net {{ $report['is_profit'] ? 'profit' : 'loss' }}</p>
            <p class="text-xl font-semibold tracking-tight font-bold mb-0 {{ $report['is_profit'] ? 'text-success' : 'text-destructive' }}">
                {{ $user->formatMoney(abs($report['net_profit'])) }}
            </p>
        </div>
    </div>
@endsection
