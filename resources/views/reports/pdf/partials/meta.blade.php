<table style="width:100%; margin-bottom:16px;">
    <tr>
        <td>
            <h1>{{ $reportTitle }}</h1>
            <div class="meta">{{ $user->name }} · {{ $user->email }}</div>
        </td>
        <td style="text-align:right;">
            <strong>Period</strong><br>{{ $periodLabel }}<br>
            <span class="meta">Generated {{ $generatedAt->format('M d, Y g:i A') }}</span>
        </td>
    </tr>
</table>
