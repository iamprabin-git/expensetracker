@php
    $generatedAt = $generatedAt ?? now();
    $statementNo = 'STM-' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT) . '-' . $generatedAt->format('Ymd');
    $institution = config('app.name');
    $creditTypes = [\App\Enums\TransactionType::Income, \App\Enums\TransactionType::Asset];
@endphp

<div class="bank-statement">
    <div class="bank-statement__brand">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <p class="bank-statement__brand-tag">{{ $documentTitle ?? 'Account Statement' }}</p>
                    <h1 class="bank-statement__brand-name">{{ $institution }}</h1>
                </td>
                <td class="bank-statement__brand-meta" align="right">
                    <p>Statement # {{ $statementNo }}</p>
                    <p>Generated {{ $generatedAt->format('d M Y, H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="bank-statement__parties-table bank-statement__parties">
        <tr>
            <td>
                <p class="bank-statement__label">Account holder</p>
                <p class="bank-statement__account-name">{{ $user->name }}</p>
                <p class="bank-statement__account-detail">{{ $user->email }}</p>
                @if ($user->phone)
                    <p class="bank-statement__account-detail">{{ $user->phone }}</p>
                @endif
                <p class="bank-statement__account-detail">Currency: {{ strtoupper($user->currency ?? 'USD') }}</p>
            </td>
            <td class="bank-statement__meta" align="right">
                <p><strong>Statement period</strong><br>{{ $periodLabel }}</p>
                <p><strong>Transactions</strong><br>{{ number_format($totals['count']) }}</p>
                <p><strong>Date</strong><br>{{ $generatedAt->format('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <table class="bank-statement__summary">
        <tr>
            <td>
                <div class="sum-label">Total credits</div>
                <div class="sum-value credit">{{ $user->formatMoney($totals['income']) }}</div>
            </td>
            <td>
                <div class="sum-label">Total debits</div>
                <div class="sum-value debit">{{ $user->formatMoney($totals['expense']) }}</div>
            </td>
            <td>
                <div class="sum-label">Closing balance</div>
                <div class="sum-value">{{ $user->formatMoney($totals['balance']) }}</div>
            </td>
            <td>
                <div class="sum-label">Line items</div>
                <div class="sum-value">{{ number_format($totals['count']) }}</div>
            </td>
        </tr>
    </table>

    <table class="bank-statement__data">
        <thead>
            <tr>
                <th style="width: 11%;">Date</th>
                <th style="width: 34%;">Description</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 13%;" class="num">Withdrawals</th>
                <th style="width: 13%;" class="num">Deposits</th>
                <th style="width: 14%;" class="num">Balance</th>
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
                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td>
                        <strong>{{ $transaction->title }}</strong>
                        @if ($transaction->description)
                            <span class="desc-sub">{{ $transaction->description }}</span>
                        @endif
                        <span class="desc-sub">{{ $transaction->type->label() }}</span>
                    </td>
                    <td>{{ $transaction->category?->name ?? '—' }}</td>
                    <td class="num">
                        @if (! $isCredit)
                            {{ $user->formatMoney($amount) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="num">
                        @if ($isCredit)
                            {{ $user->formatMoney($amount) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="num">{{ $user->formatMoney($runningBalance) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 16px;">No transactions in this period.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($transactions->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align: right;">Period totals</th>
                    <td class="num debit">{{ $user->formatMoney($totals['expense']) }}</td>
                    <td class="num credit">{{ $user->formatMoney($totals['income']) }}</td>
                    <td class="num">{{ $user->formatMoney($totals['balance']) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="bank-statement__legal">
        <strong>Important:</strong> This statement is generated from your personal records in {{ $institution }}.
        It is not issued by a licensed financial institution. {{ number_format($totals['count']) }} transaction(s) · {{ $periodLabel }} · Confidential.
    </p>
</div>
