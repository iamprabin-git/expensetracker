<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    @include('reports.pdf.styles')
</head>
<body>
    @include('reports.pdf.partials.meta')
    <table>
        <thead>
            <tr><th>Account</th><th class="num">Debit</th><th class="num">Credit</th></tr>
        </thead>
        <tbody>
            @forelse ($report['lines'] as $line)
                <tr>
                    <td>{{ $line['account'] }}</td>
                    <td class="num">{{ $line['debit'] > 0 ? $user->formatMoney($line['debit']) : '—' }}</td>
                    <td class="num">{{ $line['credit'] > 0 ? $user->formatMoney($line['credit']) : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;">No transactions in this period.</td></tr>
            @endforelse
        </tbody>
        @if (count($report['lines']) > 0)
            <tfoot>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="num">{{ $user->formatMoney($report['total_debit']) }}</td>
                    <td class="num">{{ $user->formatMoney($report['total_credit']) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
    <p class="footer">{{ config('app.name') }} — confidential.</p>
</body>
</html>
