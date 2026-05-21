@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page report-page-wrap card-panel bg-white mx-auto">
        <p class="text-sm text-muted-foreground mb-3">As of {{ $report['as_of'] }} · Based on cumulative transactions.</p>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-6">
                <h2 class="h6 font-semibold">Assets</h2>
                <div class="overflow-x-auto table-scroll-touch">
                    <table class="table table-sm table-key-value mb-0">
                        @foreach ($report['assets'] as $row)
                            <tr><td>{{ $row['name'] }}</td><td class="text-right">{{ $user->formatMoney($row['amount']) }}</td></tr>
                        @endforeach
                        <tr class="font-bold border-top"><td>Total assets</td><td class="text-right">{{ $user->formatMoney($report['total_assets']) }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="col-span-12 md:col-span-6">
                <h2 class="h6 font-semibold">Liabilities</h2>
                <div class="overflow-x-auto table-scroll-touch mb-3">
                    <table class="table table-sm table-key-value mb-0">
                        @foreach ($report['liabilities'] as $row)
                            <tr><td>{{ $row['name'] }}</td><td class="text-right">{{ $user->formatMoney($row['amount']) }}</td></tr>
                        @endforeach
                        <tr class="font-bold border-top"><td>Total liabilities</td><td class="text-right">{{ $user->formatMoney($report['total_liabilities']) }}</td></tr>
                    </table>
                </div>
                <h2 class="h6 font-semibold">Equity</h2>
                <div class="overflow-x-auto table-scroll-touch">
                    <table class="table table-sm table-key-value mb-0">
                        @foreach ($report['equity'] as $row)
                            <tr><td>{{ $row['name'] }}</td><td class="text-right">{{ $user->formatMoney($row['amount']) }}</td></tr>
                        @endforeach
                        <tr class="font-bold border-top"><td>Total equity</td><td class="text-right">{{ $user->formatMoney($report['total_equity']) }}</td></tr>
                    </table>
                </div>
                <p class="font-bold mt-3 mb-0">Liabilities + Equity: {{ $user->formatMoney($report['total_liabilities_equity']) }}</p>
            </div>
        </div>
        @if ($report['balanced'])
            <p class="text-sm text-success mb-0">✓ Assets = Liabilities + Equity</p>
        @endif
    </div>
@endsection
