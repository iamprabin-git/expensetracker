<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Transaction statement') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    @stack('styles')
    <style>
        @media print {
            .statement-no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
                color: #000 !important;
            }

            .statement-page {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased statement-body">
    <div class="statement-toolbar statement-no-print">
        <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Back to transactions</a>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
                <a href="{{ route('transactions.statement.pdf', request()->query()) }}" class="btn btn-sm btn-outline-primary">Download PDF</a>
            </div>
        </div>
    </div>

    <main class="container py-4 py-md-5">
        @yield('content')
    </main>
</body>
</html>
