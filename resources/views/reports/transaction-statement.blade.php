@extends('layouts.report')

@section('title', $reportTitle)

@section('content')
    @include('reports.partials.filters')
    @include('reports.partials.header')

    <div class="report-page card-panel bg-white mx-auto" style="max-width: 56rem;">
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="rounded-3 border p-3 text-center">
                    <p class="small text-secondary mb-1">Income</p>
                    <p class="fw-bold text-success mb-0">{{ $user->formatMoney($totals['income']) }}</p>
                </div>
            </div>
            <div class="col-4">
                <div class="rounded-3 border p-3 text-center">
                    <p class="small text-secondary mb-1">Expenses</p>
                    <p class="fw-bold text-danger mb-0">{{ $user->formatMoney($totals['expense']) }}</p>
                </div>
            </div>
            <div class="col-4">
                <div class="rounded-3 border p-3 text-center">
                    <p class="small text-secondary mb-1">Net balance</p>
                    <p class="fw-bold mb-0 {{ $totals['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ $user->formatMoney($totals['balance']) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="text-nowrap">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td>
                                <span class="fw-medium">{{ $transaction->title }}</span>
                                @if ($transaction->description)
                                    <br><span class="small text-secondary">{{ $transaction->description }}</span>
                                @endif
                            </td>
                            <td>{{ $transaction->category?->name ?? '—' }}</td>
                            <td>{{ $transaction->type->label() }}</td>
                            <td class="text-end fw-semibold {{ $transaction->isIncome() ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->isIncome() ? '+' : '-' }}{{ $user->formatMoney((float) $transaction->amount) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">No transactions in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($transactions->isNotEmpty())
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total income</th>
                            <th class="text-end text-success">{{ $user->formatMoney($totals['income']) }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Total expenses</th>
                            <th class="text-end text-danger">{{ $user->formatMoney($totals['expense']) }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Net balance</th>
                            <th class="text-end">{{ $user->formatMoney($totals['balance']) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <p class="small text-secondary mt-4 mb-0">
            This statement lists {{ $totals['count'] }} transaction(s). Generated from your recorded income and expenses.
        </p>
    </div>
@endsection
