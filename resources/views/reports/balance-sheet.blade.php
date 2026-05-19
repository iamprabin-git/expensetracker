@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page card-panel bg-white mx-auto" style="max-width: 56rem;">
        <p class="small text-secondary mb-3">As of {{ $report['as_of'] }} · Based on cumulative transactions.</p>
        <div class="row g-4">
            <div class="col-md-6">
                <h2 class="h6 fw-semibold">Assets</h2>
                <table class="table table-sm">
                    @foreach ($report['assets'] as $row)
                        <tr><td>{{ $row['name'] }}</td><td class="text-end">{{ $user->formatMoney($row['amount']) }}</td></tr>
                    @endforeach
                    <tr class="fw-bold border-top"><td>Total assets</td><td class="text-end">{{ $user->formatMoney($report['total_assets']) }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h2 class="h6 fw-semibold">Liabilities</h2>
                <table class="table table-sm mb-3">
                    @foreach ($report['liabilities'] as $row)
                        <tr><td>{{ $row['name'] }}</td><td class="text-end">{{ $user->formatMoney($row['amount']) }}</td></tr>
                    @endforeach
                    <tr class="fw-bold border-top"><td>Total liabilities</td><td class="text-end">{{ $user->formatMoney($report['total_liabilities']) }}</td></tr>
                </table>
                <h2 class="h6 fw-semibold">Equity</h2>
                <table class="table table-sm">
                    @foreach ($report['equity'] as $row)
                        <tr><td>{{ $row['name'] }}</td><td class="text-end">{{ $user->formatMoney($row['amount']) }}</td></tr>
                    @endforeach
                    <tr class="fw-bold border-top"><td>Total equity</td><td class="text-end">{{ $user->formatMoney($report['total_equity']) }}</td></tr>
                </table>
                <p class="fw-bold mt-3 mb-0">Liabilities + Equity: {{ $user->formatMoney($report['total_liabilities_equity']) }}</p>
            </div>
        </div>
        @if ($report['balanced'])
            <p class="small text-success mb-0">✓ Assets = Liabilities + Equity</p>
        @endif
    </div>
@endsection
