<x-user-layout>
    <x-slot name="header">Transactions</x-slot>
    <x-slot name="subheader">View and filter all your income and expense records.</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('reports.show', ['report' => 'transaction-statement'] + request()->only(['from_date', 'to_date'])) }}" class="btn-secondary-app">Statement</a>
        <a href="{{ route('transactions.create') }}" class="btn-primary-app">New transaction</a>
    </x-slot>

    <div class="card-panel mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="label-app" for="search">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="input-app form-control" placeholder="Search by title">
            </div>
            <div class="col-12 col-md-3">
                <label class="label-app" for="type">Type</label>
                <select name="type" id="type" class="input-app form-select">
                    <option value="">All types</option>
                    @foreach (\App\Enums\TransactionType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-5 d-flex flex-wrap gap-2">
                <button type="submit" class="btn-primary-app flex-grow-1 flex-sm-grow-0">Filter</button>
                <a href="{{ route('transactions.index') }}" class="btn-secondary-app flex-grow-1 flex-sm-grow-0">Reset</a>
            </div>
        </form>
    </div>

    <div class="card-panel">
        <div class="table-responsive table-scroll-touch">
            <table class="table table-hover align-middle mb-0 table-mobile-stack">
                <thead>
                    <tr class="text-secondary">
                        <th>Title</th>
                        <th class="d-none d-sm-table-cell">Type</th>
                        <th class="d-none d-md-table-cell">Category</th>
                        <th class="d-none d-lg-table-cell">Date</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td data-label="Title">
                                <div class="fw-medium">{{ $transaction->title }}</div>
                                <div class="text-secondary small d-lg-none">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                            </td>
                            <td class="d-none d-sm-table-cell" data-label="Type">
                                <span @class(['badge', $transaction->typeBadgeClass()])>
                                    {{ $transaction->type->label() }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell" data-label="Category">{{ $transaction->category?->name ?? '—' }}</td>
                            <td class="d-none d-lg-table-cell" data-label="Date">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td @class(['text-end fw-semibold', $transaction->amountColorClass()]) data-label="Amount">
                                {{ $transaction->amountPrefix() }}<x-money :amount="$transaction->amount" />
                            </td>
                            <td class="text-end" data-label="Actions">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    </div>
</x-user-layout>
