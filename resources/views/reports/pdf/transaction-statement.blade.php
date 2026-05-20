<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    @include('reports.pdf.styles')
    <style>
        .summary { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .summary td { width: 33.33%; border: 1px solid #e2e8f0; padding: 8px; text-align: center; }
        .summary .label { font-size: 9px; text-transform: uppercase; color: #64748b; }
        .summary .value { font-size: 12px; font-weight: bold; margin-top: 4px; }
    </style>
</head>
<body>
    @include('reports.pdf.partials.meta')
    <table class="summary">
        <tr>
            <td><div class="label">Income</div><div class="value success">{{ $user->formatMoney($totals['income']) }}</div></td>
            <td><div class="label">Expenses</div><div class="value danger">{{ $user->formatMoney($totals['expense']) }}</div></td>
            <td><div class="label">Net balance</div><div class="value">{{ $user->formatMoney($totals['balance']) }}</div></td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Category</th>
                <th>Type</th>
                <th class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                    <td>
                        {{ $transaction->title }}
                        @if ($transaction->description)
                            <br><span style="color:#64748b;font-size:9px;">{{ $transaction->description }}</span>
                        @endif
                    </td>
                    <td>{{ $transaction->category?->name ?? '—' }}</td>
                    <td>{{ $transaction->type->label() }}</td>
                    <td class="num {{ $transaction->type->value }}">
                        {{ $transaction->amountPrefix() }}{{ $user->formatMoney((float) $transaction->amount) }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">No transactions in this period.</td></tr>
            @endforelse
        </tbody>
        @if ($transactions->isNotEmpty())
            <tfoot>
                <tr class="total-row"><td colspan="4">Total income</td><td class="num success">{{ $user->formatMoney($totals['income']) }}</td></tr>
                <tr class="total-row"><td colspan="4">Total expenses</td><td class="num danger">{{ $user->formatMoney($totals['expense']) }}</td></tr>
                <tr class="total-row"><td colspan="4">Net balance</td><td class="num">{{ $user->formatMoney($totals['balance']) }}</td></tr>
            </tfoot>
        @endif
    </table>
    <p class="footer">{{ $totals['count'] }} transaction(s) listed. {{ config('app.name') }} — confidential.</p>
</body>
</html>
