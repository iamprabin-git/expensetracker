<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Transaction statement') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/css/statement.css'])
    @stack('styles')
</head>
<body class="statement-body bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="statement-toolbar statement-no-print sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
        <div class="mx-auto w-full max-w-6xl px-4 pt-3">
            @include('layouts.partials.breadcrumbs')
        </div>
        <div class="mx-auto flex w-full max-w-6xl flex-col items-stretch justify-between gap-3 px-4 pb-3 sm:flex-row sm:items-center">
            <x-ui.button variant="outline" size="sm" href="{{ route('transactions.index') }}">&larr; Back to transactions</x-ui.button>
            <div class="flex flex-wrap gap-2">
                <x-ui.button size="sm" type="button" onclick="window.print()">Print statement</x-ui.button>
                @php
                    $statementQuery = request()->query();
                    $statementExport = ['report' => 'transaction-statement'] + $statementQuery;
                @endphp
                <x-export-dropdown
                    label="Download"
                    :pdf-href="route('transactions.statement.pdf', $statementQuery)"
                    :csv-href="route('transactions.statement.export', $statementQuery + ['format' => 'csv'])"
                    :xlsx-href="route('transactions.statement.export', $statementQuery + ['format' => 'xlsx'])"
                />
            </div>
        </div>
    </div>

    <main class="statement-main mx-auto w-full max-w-6xl px-4 py-4 md:py-6">
        @yield('content')
    </main>
</body>
</html>
