<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    @include('reports.pdf.styles')
</head>
<body>
    @include('reports.pdf.partials.meta')
    <p class="meta">As of {{ $report['as_of'] }}</p>
    <p class="section-title">Assets</p>
    <table>
        @foreach ($report['assets'] as $row)
            <tr><td>{{ $row['name'] }}</td><td class="num">{{ $user->formatMoney($row['amount']) }}</td></tr>
        @endforeach
        <tr class="total-row"><td>Total assets</td><td class="num">{{ $user->formatMoney($report['total_assets']) }}</td></tr>
    </table>
    <p class="section-title">Liabilities</p>
    <table>
        @foreach ($report['liabilities'] as $row)
            <tr><td>{{ $row['name'] }}</td><td class="num">{{ $user->formatMoney($row['amount']) }}</td></tr>
        @endforeach
        <tr class="total-row"><td>Total liabilities</td><td class="num">{{ $user->formatMoney($report['total_liabilities']) }}</td></tr>
    </table>
    <p class="section-title">Equity</p>
    <table>
        @foreach ($report['equity'] as $row)
            <tr><td>{{ $row['name'] }}</td><td class="num">{{ $user->formatMoney($row['amount']) }}</td></tr>
        @endforeach
        <tr class="total-row"><td>Total equity</td><td class="num">{{ $user->formatMoney($report['total_equity']) }}</td></tr>
    </table>
    <p><strong>Liabilities + Equity:</strong> {{ $user->formatMoney($report['total_liabilities_equity']) }}</p>
    <p class="footer">{{ config('app.name') }} — confidential.</p>
</body>
</html>
