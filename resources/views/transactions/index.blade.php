@php
    $typeBadgeVariant = fn (\App\Enums\TransactionType $type) => match ($type) {
        \App\Enums\TransactionType::Income => 'success',
        \App\Enums\TransactionType::Expense => 'destructive',
        \App\Enums\TransactionType::Asset => 'secondary',
        \App\Enums\TransactionType::Liability => 'outline',
    };
@endphp

@push('styles')
    @vite(['resources/css/transactions-page.css'])
@endpush

<x-user-layout>
    <x-slot name="header">Transactions</x-slot>
    <x-slot name="subheader">View and filter all your income and expense records.</x-slot>
    <x-slot name="headerActions">
        @php
            $listExportQuery = request()->only(['search', 'type']);
        @endphp
        <x-ui.button
            variant="outline"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#transaction-import-modal"
        >
            Import
        </x-ui.button>
        <x-export-dropdown
            label="Export"
            :pdf-href="route('transactions.statement.pdf', $listExportQuery)"
            :csv-href="route('transactions.export', $listExportQuery + ['format' => 'csv'])"
            :xlsx-href="route('transactions.export', $listExportQuery + ['format' => 'xlsx'])"
        />
        <x-ui.button variant="outline" href="{{ route('reports.show', ['report' => 'transaction-statement'] + request()->only(['search', 'type', 'from_date', 'to_date'])) }}">Statement</x-ui.button>
        <x-ui.button href="{{ route('transactions.create') }}">New transaction</x-ui.button>
    </x-slot>

    <div class="transactions-page">
        @if (session('import_errors'))
            <div class="transactions-page__import-result" role="status">
                @if (session('success'))
                    <p class="transactions-page__import-result-success mb-2">{{ session('success') }}</p>
                @endif
                @if (count(session('import_errors')) > 0)
                    <p class="mb-1 text-sm font-medium">Import issues:</p>
                    <ul class="mb-0 ps-4 text-sm text-muted-foreground">
                        @foreach (session('import_errors') as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="transactions-page__summary">
            <div class="transactions-page__stat">
                <p class="transactions-page__stat-label">Matching records</p>
                <p class="transactions-page__stat-value">{{ number_format($summary['count']) }}</p>
            </div>
            <div class="transactions-page__stat">
                <p class="transactions-page__stat-label">Total income</p>
                <p class="transactions-page__stat-value transactions-page__stat-value--income">
                    <x-money :amount="$summary['income']" />
                </p>
            </div>
            <div class="transactions-page__stat">
                <p class="transactions-page__stat-label">Total expenses</p>
                <p class="transactions-page__stat-value transactions-page__stat-value--expense">
                    <x-money :amount="$summary['expense']" />
                </p>
            </div>
            <div class="transactions-page__stat transactions-page__stat--balance">
                <p class="transactions-page__stat-label">Net balance</p>
                <p @class([
                    'transactions-page__stat-value',
                    'transactions-page__stat-value--income' => $summary['balance'] >= 0,
                    'transactions-page__stat-value--expense' => $summary['balance'] < 0,
                ])>
                    <x-money :amount="$summary['balance']" />
                </p>
            </div>
        </div>

        <section class="transactions-page__filters" aria-label="Filter transactions">
            <h2 class="transactions-page__filters-title">Search & filter</h2>
            <form method="GET" class="transactions-page__filters-form">
                <div>
                    <x-ui.label for="search">Search</x-ui.label>
                    <x-ui.input
                        type="search"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search by title…"
                    />
                </div>
                <div>
                    <x-ui.label for="type">Type</x-ui.label>
                    <x-ui.select name="type" id="type">
                        <option value="">All types</option>
                        @foreach (\App\Enums\TransactionType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="transactions-page__filters-actions">
                    <x-ui.button type="submit">Apply filters</x-ui.button>
                    @if ($hasFilters)
                        <x-ui.button variant="outline" href="{{ route('transactions.index') }}">Clear</x-ui.button>
                    @endif
                </div>
            </form>
        </section>

        <section class="transactions-page__list" aria-label="Transaction list">
            <div class="transactions-page__list-header">
                <h2 class="transactions-page__list-title">All transactions</h2>
                <p class="transactions-page__list-meta">
                    Showing {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}
                    @if ($hasFilters)
                        · filtered
                    @endif
                </p>
            </div>

            @if ($transactions->isEmpty())
                <div class="transactions-page__empty">
                    <span class="transactions-page__empty-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797-2.101c.88-.218 1.594-.966 1.594-1.854 0-1.103-.84-2.007-1.938-2.007H2.25M2.25 15h15.75M2.25 12h15.75m-13.5-6.75h13.5a2.25 2.25 0 0 1 2.25 2.25v.75H2.25V7.5A2.25 2.25 0 0 1 4.5 5.25Z" />
                        </svg>
                    </span>
                    <h3 class="transactions-page__empty-title">No transactions found</h3>
                    <p class="transactions-page__empty-text">
                        @if ($hasFilters)
                            Try adjusting your search or filters, or clear them to see all records.
                        @else
                            Record your first income or expense to start tracking your money.
                        @endif
                    </p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @if ($hasFilters)
                            <x-ui.button variant="outline" href="{{ route('transactions.index') }}">Clear filters</x-ui.button>
                        @endif
                        <x-ui.button href="{{ route('transactions.create') }}">Add transaction</x-ui.button>
                    </div>
                </div>
            @else
                <x-ui.table class="transactions-page__table transactions-page__table--responsive mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Transaction</th>
                            <th scope="col" class="hidden sm:table-cell">Type</th>
                            <th scope="col" class="hidden md:table-cell">Category</th>
                            <th scope="col" class="hidden lg:table-cell">Date</th>
                            <th scope="col" class="text-right">Amount</th>
                            <th scope="col" class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td data-label="Transaction">
                                    <div class="transactions-page__title-cell">
                                        <p class="transactions-page__title">{{ $transaction->title }}</p>
                                        <div class="transactions-page__title-meta lg:hidden">
                                            <span>{{ $transaction->transaction_date->format('M d, Y') }}</span>
                                            <x-ui.badge :variant="$typeBadgeVariant($transaction->type)">{{ $transaction->type->label() }}</x-ui.badge>
                                        </div>
                                        @if ($transaction->receipt_image_path)
                                            <div class="transactions-page__title-meta">
                                                <span class="transactions-page__receipt-dot">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.163-5.163a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v8.25A1.5 1.5 0 0 0 3.75 15.75Zm13.5-9.75a2.25 2.25 0 0 1 2.25 2.25v.75" />
                                                    </svg>
                                                    Receipt attached
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell" data-label="Type">
                                    <x-ui.badge :variant="$typeBadgeVariant($transaction->type)">{{ $transaction->type->label() }}</x-ui.badge>
                                </td>
                                <td class="hidden md:table-cell text-muted-foreground" data-label="Category">
                                    {{ $transaction->category?->name ?? '—' }}
                                </td>
                                <td class="hidden lg:table-cell text-muted-foreground whitespace-nowrap" data-label="Date">
                                    {{ $transaction->transaction_date->format('M d, Y') }}
                                </td>
                                <td @class(['text-right transactions-page__amount', $transaction->amountColorClass()]) data-label="Amount">
                                    {{ $transaction->amountPrefix() }}<x-money :amount="$transaction->amount" />
                                </td>
                                <td data-label="Actions">
                                    <div class="transactions-page__actions">
                                        <x-ui.button variant="outline" size="sm" href="{{ route('transactions.edit', $transaction) }}">Edit</x-ui.button>
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="destructive" size="sm">Delete</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>

                @if ($transactions->hasPages())
                    <div class="transactions-page__pagination">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>

    @include('transactions.partials.import-modal')
</x-user-layout>
