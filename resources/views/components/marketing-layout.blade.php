@props([
    'title' => null,
    'metaDescription' => null,
    'narrow' => false,
    'breadcrumbs' => null,
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
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="site-body flex min-h-screen flex-col bg-background text-foreground antialiased">
    @include('layouts.partials.flash-toasts')
    @include('layouts.partials.site-header')

    <main class="site-main flex-1 {{ $narrow ? 'site-main--narrow' : '' }}">
        <div class="site-breadcrumb-bar {{ $narrow ? 'mx-auto w-full max-w-lg px-4' : 'mx-auto w-full max-w-6xl px-4' }}">
            @include('layouts.partials.breadcrumbs', ['items' => $breadcrumbs])
        </div>
        {{ $slot }}
    </main>

    @include('layouts.partials.site-footer')
    @stack('scripts')
</body>
</html>
