<x-user-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="subheader">Track your income, expenses, and balance at a glance.</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('transactions.create') }}" class="btn-primary-app">Add transaction</a>
    </x-slot>

    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Total income</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"><x-money :amount="$income" /></p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Total expenses</p>
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400"><x-money :amount="$expense" /></p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Net balance</p>
                <p @class(['text-2xl font-bold', $balance >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400'])>
                    <x-money :amount="$balance" />
                </p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">This month</p>
                <p class="text-sm mb-0"><span class="text-emerald-600 dark:text-emerald-400">+<x-money :amount="$monthlyIncome" /></span> / <span class="text-rose-600 dark:text-rose-400">-<x-money :amount="$monthlyExpense" /></span></p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-semibold mb-0">Recent transactions</h2>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400">View all</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-secondary">
                                <th>Title</th>
                                <th class="d-none d-md-table-cell">Category</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="fw-medium">{{ $transaction->title }}</td>
                                    <td class="d-none d-md-table-cell">
                                        @if ($transaction->category)
                                            <span class="badge rounded-pill" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                                {{ $transaction->category->name }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                    <td class="text-end fw-semibold {{ $transaction->isIncome() ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->isIncome() ? '+' : '-' }}<x-money :amount="$transaction->amount" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">No transactions yet. Add your first one!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card-panel h-100">
                <h2 class="h5 fw-semibold mb-3">Quick actions</h2>
                <div class="d-grid gap-2">
                    <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="btn btn-success">Record income</a>
                    <a href="{{ route('transactions.create', ['type' => 'expense']) }}" class="btn btn-danger">Record expense</a>
                    <a href="{{ route('categories.create') }}" class="btn-secondary-app">Manage categories</a>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
