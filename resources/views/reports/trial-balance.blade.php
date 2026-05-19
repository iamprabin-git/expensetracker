@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page card-panel bg-white mx-auto" style="max-width: 56rem;">
        <p class="small text-secondary mb-3">Derived from your income and expense transactions by category.</p>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Account</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['lines'] as $line)
                        <tr>
                            <td>{{ $line['account'] }}</td>
                            <td class="text-end">{{ $line['debit'] > 0 ? $user->formatMoney($line['debit']) : '—' }}</td>
                            <td class="text-end">{{ $line['credit'] > 0 ? $user->formatMoney($line['credit']) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">No transactions in this period.</td></tr>
                    @endforelse
                </tbody>
                @if (count($report['lines']) > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>Total</td>
                            <td class="text-end">{{ $user->formatMoney($report['total_debit']) }}</td>
                            <td class="text-end">{{ $user->formatMoney($report['total_credit']) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if ($report['balanced'] ?? false)
            <p class="small text-success mb-0">✓ Trial balance is balanced (total debits = total credits).</p>
        @endif
    </div>
@endsection
