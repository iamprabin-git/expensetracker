@extends('layouts.statement')

@section('title', 'Transaction statement')

@section('content')
    <div class="statement-page card-panel bg-white mx-auto">
        <div class="statement-header border-bottom pb-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <p class="text-uppercase small fw-semibold text-secondary mb-1 letter-spacing">Account statement</p>
                    <h1 class="h4 fw-bold mb-1">{{ config('app.name') }}</h1>
                    <p class="mb-0 text-secondary">{{ $user->name }} · {{ $user->email }}</p>
                    @if ($user->phone)
                        <p class="mb-0 text-secondary small">{{ $user->phone }}</p>
                    @endif
                </div>
                <div class="text-md-end">
                    <p class="mb-1 small text-secondary">Period</p>
                    <p class="fw-semibold mb-1">{{ $periodLabel }}</p>
                    <p class="mb-0 small text-secondary">Generated {{ now()->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="statement-no-print mb-4">
            <form method="GET" action="{{ route('transactions.statement') }}" class="row g-3 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="label-app" for="from_date">From</label>
                    <input type="date" name="from_date" id="from_date" value="{{ $filters['from_date'] }}" class="form-control input-app">
                </div>
                <div class="col-6 col-md-3">
                    <label class="label-app" for="to_date">To</label>
                    <input type="date" name="to_date" id="to_date" value="{{ $filters['to_date'] }}" class="form-control input-app">
                </div>
                <div class="col-12 col-md-3">
                    <label class="label-app" for="type">Type</label>
                    <select name="type" id="type" class="form-select input-app">
                        <option value="">All</option>
                        @foreach (\App\Enums\TransactionType::cases() as $type)
                            <option value="{{ $type->value }}" @selected($filters['type'] === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Apply</button>
                    <a href="{{ route('transactions.statement') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4">
                <div class="rounded-3 border p-3 text-center">
                    <p class="small text-secondary mb-1">Income</p>
                    <p class="fw-bold text-success mb-0"><x-money :amount="$totals['income']" :user="$user" /></p>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="rounded-3 border p-3 text-center">
                    <p class="small text-secondary mb-1">Expenses</p>
                    <p class="fw-bold text-danger mb-0"><x-money :amount="$totals['expense']" :user="$user" /></p>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="rounded-3 border p-3 text-center">
                    <p class="small text-secondary mb-1">Net balance</p>
                    <p class="fw-bold mb-0 {{ $totals['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                        <x-money :amount="$totals['balance']" :user="$user" />
                    </p>
                </div>
            </div>
        </div>

        <div class="table-responsive table-scroll-touch">
            <table class="table table-bordered align-middle mb-0 statement-table table-mobile-stack">
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
                            <td class="text-nowrap" data-label="Date">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td data-label="Title">
                                <span class="fw-medium">{{ $transaction->title }}</span>
                                @if ($transaction->description)
                                    <br><span class="small text-secondary">{{ $transaction->description }}</span>
                                @endif
                            </td>
                            <td data-label="Category">{{ $transaction->category?->name ?? '—' }}</td>
                            <td data-label="Type">{{ $transaction->type->label() }}</td>
                            <td @class(['text-end fw-semibold', $transaction->amountColorClass()]) data-label="Amount">
                                {{ $transaction->amountPrefix() }}<x-money :amount="$transaction->amount" :user="$user" />
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
                            <th class="text-end text-success"><x-money :amount="$totals['income']" :user="$user" /></th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Total expenses</th>
                            <th class="text-end text-danger"><x-money :amount="$totals['expense']" :user="$user" /></th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Net balance</th>
                            <th class="text-end"><x-money :amount="$totals['balance']" :user="$user" /></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <p class="small text-secondary mt-4 mb-0 statement-footer">
            This statement lists {{ $totals['count'] }} transaction(s). For questions, contact support via the website.
        </p>
    </div>
@endsection
