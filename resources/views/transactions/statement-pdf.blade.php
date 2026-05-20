<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Transaction Statement</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 24px;
        }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #64748b; margin-bottom: 20px; }
        .header-table { width: 100%; margin-bottom: 20px; }
        .header-table td { vertical-align: top; }
        .summary { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary td {
            width: 33.33%;
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
        }
        .summary .label { font-size: 9px; text-transform: uppercase; color: #64748b; }
        .summary .value { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .income { color: #059669; }
        .expense { color: #e11d48; }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.data th,
        table.data td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        table.data th {
            background: #f1f5f9;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data td.amount { text-align: right; white-space: nowrap; }
        table.data tfoot th {
            text-align: right;
            background: #f8fafc;
        }
        .footer {
            margin-top: 24px;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <h1>{{ config('app.name') }} — Transaction Statement</h1>
                <div class="meta">{{ $user->name }} · {{ $user->email }}</div>
            </td>
            <td style="text-align: right;">
                <strong>Period</strong><br>{{ $periodLabel }}<br>
                <span class="meta">Generated {{ $generatedAt->format('M d, Y g:i A') }}</span>
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total income</div>
                <div class="value income">{{ $user->formatMoney($totals['income']) }}</div>
            </td>
            <td>
                <div class="label">Total expenses</div>
                <div class="value expense">{{ $user->formatMoney($totals['expense']) }}</div>
            </td>
            <td>
                <div class="label">Net balance</div>
                <div class="value">{{ $user->formatMoney($totals['balance']) }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 14%;">Date</th>
                <th style="width: 32%;">Title</th>
                <th style="width: 18%;">Category</th>
                <th style="width: 12%;">Type</th>
                <th style="width: 14%;">Amount</th>
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
                    <td class="amount {{ $transaction->type->value }}">
                        {{ $transaction->amountPrefix() }}{{ $user->formatMoney((float) $transaction->amount) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 16px;">No transactions in this period.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($transactions->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="4">Total income</th>
                    <th class="amount income">{{ $user->formatMoney($totals['income']) }}</th>
                </tr>
                <tr>
                    <th colspan="4">Total expenses</th>
                    <th class="amount expense">{{ $user->formatMoney($totals['expense']) }}</th>
                </tr>
                <tr>
                    <th colspan="4">Net balance</th>
                    <th class="amount">{{ $user->formatMoney($totals['balance']) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer">{{ $totals['count'] }} transaction(s) listed. {{ config('app.name') }} — confidential.</p>
</body>
</html>
