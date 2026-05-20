@props([
    'title' => null,
    'metaDescription' => null,
    'narrow' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <title>{{ $title ? $title.' — ' : '' }}{{ $company?->company_name ?? config('app.name') }}</title>
    @if ($company?->faviconUrl())
        <link rel="icon" href="{{ $company->faviconUrl() }}" type="image/png">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-body d-flex flex-column min-vh-100 bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    @include('layouts.partials.site-header')

    <main class="site-main flex-grow-1 {{ $narrow ? 'site-main--narrow' : '' }}">
        @if (session('success'))
            <div class="container {{ $narrow ? 'pb-0' : 'pt-4' }}">
                <div class="alert alert-success border-0 rounded-3 shadow-sm mb-0" role="alert">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        {{ $slot }}
    </main>

    @include('layouts.partials.site-footer')
</body>
</html>
