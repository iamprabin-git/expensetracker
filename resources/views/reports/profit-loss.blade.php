@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page report-page-wrap card-panel bg-white mx-auto">
        <h2 class="h6 fw-semibold text-success">Revenue</h2>
        <div class="table-responsive table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @forelse ($report['revenue'] as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-end">{{ $user->formatMoney($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-secondary">No income in period</td></tr>
                    @endforelse
                    <tr class="fw-bold border-top">
                        <td>Total revenue</td>
                        <td class="text-end text-success">{{ $user->formatMoney($report['total_revenue']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="h6 fw-semibold text-danger">Expenses</h2>
        <div class="table-responsive table-scroll-touch mb-4">
            <table class="table table-sm table-key-value mb-0">
                <tbody>
                    @forelse ($report['expenses'] as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-end">{{ $user->formatMoney($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-secondary">No expenses in period</td></tr>
                    @endforelse
                    <tr class="fw-bold border-top">
                        <td>Total expenses</td>
                        <td class="text-end text-danger">{{ $user->formatMoney($report['total_expenses']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rounded-3 border p-4 {{ $report['is_profit'] ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
            <p class="mb-1 small text-secondary">Net {{ $report['is_profit'] ? 'profit' : 'loss' }}</p>
            <p class="h4 fw-bold mb-0 {{ $report['is_profit'] ? 'text-success' : 'text-danger' }}">
                {{ $user->formatMoney(abs($report['net_profit'])) }}
            </p>
        </div>
    </div>
@endsection
