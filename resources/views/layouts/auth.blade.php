<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}{{ $company?->company_name ?? config('app.name') }}</title>
    @if ($company?->faviconUrl())
        <link rel="icon" href="{{ $company->faviconUrl() }}" type="image/png">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page font-sans antialiased">
    @include('layouts.partials.flash-toasts')

    <div class="auth-shell">
        <aside class="auth-hero" aria-label="Product overview">
            @include('auth.partials.hero', ['mode' => $mode])
        </aside>

        <div class="auth-panel">
            <header class="auth-panel__top">
                <a href="{{ url('/') }}" class="auth-panel__brand lg:hidden">
                    <x-site-brand tag="span" />
                </a>
                <div class="auth-panel__actions">
                    <x-ui.theme-toggle />
                    <a href="{{ url('/') }}" class="auth-panel__back">{{ __('Back to site') }}</a>
                </div>
            </header>

            <main class="auth-panel__main">
                <div class="auth-card">
                    @include('auth.partials.switcher')
                    {{ $slot }}
                </div>
            </main>

            <footer class="auth-panel__footer">
                <p>&copy; {{ now()->year }} {{ $company?->company_name ?? config('app.name') }}. All rights reserved.</p>
            </footer>
        </div>
    </div>
</body>
</html>
