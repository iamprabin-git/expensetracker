<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    @include('reports.pdf.styles')
</head>
<body>
    @include('reports.pdf.partials.meta')
    <p class="section-title">Operating activities</p>
    <table>
        @foreach ($report['operating'] as $row)
            <tr @if($row['bold'] ?? false) class="total-row" @endif>
                <td>{{ $row['label'] }}</td>
                <td class="num">{{ $user->formatMoney($row['amount']) }}</td>
            </tr>
        @endforeach
    </table>
    <p class="section-title">Investing activities</p>
    <table>
        @foreach ($report['investing'] as $row)
            <tr @if($row['bold'] ?? false) class="total-row" @endif>
                <td>{{ $row['label'] }}</td>
                <td class="num">{{ $user->formatMoney($row['amount']) }}</td>
            </tr>
        @endforeach
    </table>
    <p class="section-title">Financing activities</p>
    <table>
        @foreach ($report['financing'] as $row)
            <tr @if($row['bold'] ?? false) class="total-row" @endif>
                <td>{{ $row['label'] }}</td>
                <td class="num">{{ $user->formatMoney($row['amount']) }}</td>
            </tr>
        @endforeach
    </table>
    <p><strong>Net change in cash:</strong> {{ $user->formatMoney($report['summary']['net_change']) }}<br>
        <strong>Opening cash:</strong> {{ $user->formatMoney($report['summary']['opening_cash']) }}<br>
        <strong>Closing cash:</strong> {{ $user->formatMoney($report['summary']['closing_cash']) }}</p>
    @if (count($report['monthly']) > 0)
        <p class="section-title">Monthly cash movement</p>
        <table>
            <thead><tr><th>Month</th><th class="num">Inflow</th><th class="num">Outflow</th><th class="num">Net</th></tr></thead>
            <tbody>
                @foreach ($report['monthly'] as $m)
                    <tr>
                        <td>{{ $m['month'] }}</td>
                        <td class="num success">{{ $user->formatMoney($m['inflow']) }}</td>
                        <td class="num danger">{{ $user->formatMoney($m['outflow']) }}</td>
                        <td class="num">{{ $user->formatMoney($m['net']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <p class="footer">{{ config('app.name') }} — confidential.</p>
</body>
</html>
