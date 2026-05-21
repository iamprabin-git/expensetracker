@props([
    'user',
    'transactions',
    'totals',
    'periodLabel',
    'generatedAt' => null,
    'filters' => null,
    'filterAction' => null,
    'documentTitle' => 'Account Statement',
])

@php
    if (! isset($company)) {
        try {
            $company = app(\App\Services\CompanySettingService::class)->get();
        } catch (\Throwable) {
            $company = null;
        }
    }

    $generatedAt = $generatedAt ?? now();
    $statementNo = 'STM-' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT) . '-' . $generatedAt->format('Ymd');
    $institution = $company?->company_name ?? config('app.name');

    $creditTypes = [\App\Enums\TransactionType::Income, \App\Enums\TransactionType::Asset];
@endphp

<article class="bank-statement" aria-label="Account statement">
    <header class="bank-statement__brand">
        <div>
            <p class="bank-statement__brand-tag">{{ $documentTitle }}</p>
            <h1 class="bank-statement__brand-name">{{ $institution }}</h1>
        </div>
        <div class="bank-statement__brand-meta">
            <p>Statement # {{ $statementNo }}</p>
            <p>Printed {{ $generatedAt->format('d M Y, H:i') }}</p>
        </div>
    </header>

    <div class="bank-statement__body">
        <div class="bank-statement__parties">
            <div>
                <p class="bank-statement__label">Account holder</p>
                <p class="bank-statement__account-name">{{ $user->name }}</p>
                <p class="bank-statement__account-detail">{{ $user->email }}</p>
                @if ($user->phone)
                    <p class="bank-statement__account-detail">{{ $user->phone }}</p>
                @endif
                <p class="bank-statement__account-detail">Currency: {{ strtoupper($user->currency ?? 'USD') }}</p>
            </div>
            <dl class="bank-statement__meta-grid">
                <div class="bank-statement__meta-row">
                    <dt>Statement period</dt>
                    <dd>{{ $periodLabel }}</dd>
                </div>
                <div class="bank-statement__meta-row">
                    <dt>Transactions</dt>
                    <dd>{{ number_format($totals['count']) }}</dd>
                </div>
                <div class="bank-statement__meta-row">
                    <dt>Generated</dt>
                    <dd>{{ $generatedAt->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        @if ($filters !== null && $filterAction !== null)
            <div class="bank-statement__filters statement-no-print">
                <form method="GET" action="{{ $filterAction }}" class="grid grid-cols-12 gap-3 items-end">
                    <div class="col-span-6 md:col-span-3">
                        <x-ui.label for="from_date">From</x-ui.label>
                        <x-ui.input type="date" name="from_date" id="from_date" value="{{ $filters['from_date'] ?? '' }}" />
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <x-ui.label for="to_date">To</x-ui.label>
                        <x-ui.input type="date" name="to_date" id="to_date" value="{{ $filters['to_date'] ?? '' }}" />
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <x-ui.label for="type">Type</x-ui.label>
                        <x-ui.select name="type" id="type">
                            <option value="">All types</option>
                            @foreach (\App\Enums\TransactionType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="col-span-12 md:col-span-3 flex flex-wrap gap-2">
                        <x-ui.button type="submit" size="sm" class="flex-1">Apply</x-ui.button>
                        <x-ui.button variant="outline" size="sm" href="{{ $filterAction }}">Clear</x-ui.button>
                    </div>
                </form>
            </div>
        @endif

        <div class="bank-statement__summary">
            <div class="bank-statement__summary-card">
                <p class="bank-statement__summary-label">Total credits</p>
                <p class="bank-statement__summary-value bank-statement__summary-value--credit">
                    <x-money :amount="$totals['income']" :user="$user" />
                </p>
            </div>
            <div class="bank-statement__summary-card">
                <p class="bank-statement__summary-label">Total debits</p>
                <p class="bank-statement__summary-value bank-statement__summary-value--debit">
                    <x-money :amount="$totals['expense']" :user="$user" />
                </p>
            </div>
            <div class="bank-statement__summary-card bank-statement__summary-card--highlight">
                <p class="bank-statement__summary-label">Closing balance</p>
                <p class="bank-statement__summary-value">
                    <x-money :amount="$totals['balance']" :user="$user" />
                </p>
            </div>
            <div class="bank-statement__summary-card">
                <p class="bank-statement__summary-label">Line items</p>
                <p class="bank-statement__summary-value">{{ number_format($totals['count']) }}</p>
            </div>
        </div>

        <div class="bank-statement__table-wrap">
            <table class="bank-statement__table bank-statement__table--responsive">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Description</th>
                        <th scope="col">Category</th>
                        <th scope="col" class="num">Withdrawals</th>
                        <th scope="col" class="num">Deposits</th>
                        <th scope="col" class="num">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = 0.0; @endphp
                    @forelse ($transactions as $transaction)
                        @php
                            $amount = (float) $transaction->amount;
                            $isCredit = in_array($transaction->type, $creditTypes, true);
                            $runningBalance += $isCredit ? $amount : -$amount;
                        @endphp
                        <tr>
                            <td data-label="Date">{{ $transaction->transaction_date->format('d M Y') }}</td>
                            <td data-label="Description">
                                <span class="desc-title">{{ $transaction->title }}</span>
                                @if ($transaction->description)
                                    <span class="desc-sub">{{ $transaction->description }}</span>
                                @endif
                                <span class="type-pill">{{ $transaction->type->label() }}</span>
                            </td>
                            <td data-label="Category">{{ $transaction->category?->name ?? '—' }}</td>
                            <td class="num" data-label="Withdrawals">
                                @if (! $isCredit)
                                    <x-money :amount="$amount" :user="$user" />
                                @else
                                    —
                                @endif
                            </td>
                            <td class="num" data-label="Deposits">
                                @if ($isCredit)
                                    <x-money :amount="$amount" :user="$user" />
                                @else
                                    —
                                @endif
                            </td>
                            <td class="num" data-label="Balance">
                                <x-money :amount="$runningBalance" :user="$user" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="bank-statement__empty">No transactions in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($transactions->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th scope="row" colspan="3">Period totals</th>
                            <td class="num bank-statement__summary-value--debit">
                                <x-money :amount="$totals['expense']" :user="$user" />
                            </td>
                            <td class="num bank-statement__summary-value--credit">
                                <x-money :amount="$totals['income']" :user="$user" />
                            </td>
                            <td class="num">
                                <x-money :amount="$totals['balance']" :user="$user" />
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <footer class="bank-statement__legal">
            <p class="mb-1">
                <strong>Important:</strong> This statement is generated from your personal records in {{ $institution }}.
                It is not issued by a licensed financial institution. Retain for your records.
            </p>
            <p class="mb-0">
                {{ number_format($totals['count']) }} transaction(s) · Period: {{ $periodLabel }} · Confidential.
            </p>
        </footer>
    </div>
</article>
