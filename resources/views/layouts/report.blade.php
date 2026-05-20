<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', $reportTitle ?? 'Report') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .report-no-print { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .report-page { box-shadow: none !important; border: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="report-toolbar report-no-print border-bottom bg-white dark:bg-slate-900 sticky-top">
        <div class="container-fluid px-3 px-sm-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">&larr; All reports</a>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @isset($reportKey)
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Switch report
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reports.show', ['report' => 'trial-balance'] + request()->query()) }}">Trial Balance</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.show', ['report' => 'profit-loss'] + request()->query()) }}">Profit & Loss</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.show', ['report' => 'balance-sheet'] + request()->query()) }}">Balance Sheet</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.show', ['report' => 'cash-flow'] + request()->query()) }}">Cash Flow</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.show', ['report' => 'transaction-statement'] + request()->query()) }}">Transaction Statement</a></li>
                        </ul>
                    </div>
                @endisset
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
                <a href="{{ route('reports.pdf', ['report' => $reportKey ?? 'trial-balance'] + request()->query()) }}" class="btn btn-sm btn-outline-primary">Download PDF</a>
            </div>
        </div>
    </div>

    <main class="container-fluid px-3 px-sm-4 py-4 py-md-5 report-main">
        @yield('content')
    </main>
</body>
</html>
