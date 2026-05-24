@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-doc report-page-wrap mx-auto">
        <div class="report-doc__body">
            <p class="mb-4 text-sm text-muted-foreground">Derived from your income and expense transactions by category.</p>
            <div class="report-table-wrap">
                <div class="overflow-x-auto table-scroll-touch">
                    <table class="report-table table-mobile-stack">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['lines'] as $line)
                                <tr>
                                    <td data-label="Account">{{ $line['account'] }}</td>
                                    <td class="text-right" data-label="Debit">{{ $line['debit'] > 0 ? $user->formatMoney($line['debit']) : '—' }}</td>
                                    <td class="text-right" data-label="Credit">{{ $line['credit'] > 0 ? $user->formatMoney($line['credit']) : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted-foreground py-4">No transactions in this period.</td></tr>
                            @endforelse
                        </tbody>
                        @if (count($report['lines']) > 0)
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td class="text-right">{{ $user->formatMoney($report['total_debit']) }}</td>
                                    <td class="text-right">{{ $user->formatMoney($report['total_credit']) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            @if ($report['balanced'] ?? false)
                <p class="mb-0 border-t border-border pt-3 text-sm text-emerald-600 dark:text-emerald-400">✓ Trial balance is balanced (total debits = total credits).</p>
            @endif
        </div>
    </div>
@endsection
