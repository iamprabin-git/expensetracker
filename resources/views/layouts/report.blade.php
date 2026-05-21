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
    @stack('styles')
    <style>
        @media print {
            .report-no-print,
            .report-toolbar { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .report-page { box-shadow: none !important; border: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-background font-sans text-foreground antialiased">
    @include('layouts.partials.flash-toasts')
    <div class="report-toolbar report-no-print sticky top-0 z-30 border-b border-border bg-card/95 backdrop-blur">
        <div class="w-full max-w-6xl mx-auto px-3 sm:px-4 pt-3">
            @include('layouts.partials.breadcrumbs')
        </div>
        <div class="w-full max-w-6xl mx-auto px-3 sm:px-4 pb-3 flex flex-wrap items-center justify-between gap-3">
            <x-ui.button variant="outline" size="sm" href="{{ route('reports.index') }}">&larr; All reports</x-ui.button>
            <div class="flex flex-wrap gap-2 items-center">
                @include('components.theme-toggle')
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
                <x-ui.button size="sm" type="button" onclick="window.print()">Print statement</x-ui.button>
                @php
                    $exportParams = ['report' => $reportKey ?? 'trial-balance'] + request()->query();
                @endphp
                <x-export-dropdown
                    label="Download"
                    :pdf-href="route('reports.pdf', $exportParams)"
                    :csv-href="route('reports.export', $exportParams + ['format' => 'csv'])"
                    :xlsx-href="route('reports.export', $exportParams + ['format' => 'xlsx'])"
                />
            </div>
        </div>
    </div>

    <main class="w-full max-w-6xl mx-auto px-3 sm:px-4 py-4 md:py-5 report-main">
        @yield('content')
    </main>
</body>
</html>
