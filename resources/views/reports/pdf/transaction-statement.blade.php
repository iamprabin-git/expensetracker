<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    @include('statements.pdf.styles')
</head>
<body>
    @include('statements.pdf.document', [
        'user' => $user,
        'transactions' => $transactions,
        'totals' => $totals,
        'periodLabel' => $periodLabel,
        'generatedAt' => $generatedAt,
        'documentTitle' => $reportTitle,
    ])
</body>
</html>
