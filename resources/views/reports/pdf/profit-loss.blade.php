<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    @include('reports.pdf.styles')
</head>
<body>
    @include('reports.pdf.partials.meta')
    <p class="section-title success">Revenue</p>
    <table>
        @forelse ($report['revenue'] as $row)
            <tr><td>{{ $row['name'] }}</td><td class="num">{{ $user->formatMoney($row['total']) }}</td></tr>
        @empty
            <tr><td colspan="2">No income in period</td></tr>
        @endforelse
        <tr class="total-row"><td>Total revenue</td><td class="num success">{{ $user->formatMoney($report['total_revenue']) }}</td></tr>
    </table>
    <p class="section-title danger">Expenses</p>
    <table>
        @forelse ($report['expenses'] as $row)
            <tr><td>{{ $row['name'] }}</td><td class="num">{{ $user->formatMoney($row['total']) }}</td></tr>
        @empty
            <tr><td colspan="2">No expenses in period</td></tr>
        @endforelse
        <tr class="total-row"><td>Total expenses</td><td class="num danger">{{ $user->formatMoney($report['total_expenses']) }}</td></tr>
    </table>
    <p><strong>Net {{ $report['is_profit'] ? 'profit' : 'loss' }}:</strong>
        {{ $user->formatMoney(abs($report['net_profit'])) }}</p>
    <p class="footer">{{ config('app.name') }} — confidential.</p>
</body>
</html>
