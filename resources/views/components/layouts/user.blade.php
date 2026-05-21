@props(['title' => null, 'header' => null, 'subheader' => null, 'headerActions' => null, 'breadcrumbs' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? ($header ?? ($company?->company_name ?? config('app.name'))) }}</title>
    @if ($company?->faviconUrl())
        <link rel="icon" href="{{ $company->faviconUrl() }}" type="image/png">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-background font-sans text-foreground antialiased">
    @include('layouts.partials.flash-toasts')
    <div class="user-shell min-h-full" data-user-shell>
        <div class="user-shell__backdrop" data-user-sidebar-backdrop aria-hidden="true"></div>

        <div class="user-shell__layout">
            @include('layouts.partials.user-sidebar')

            <div class="user-shell__content">
                @include('layouts.partials.user-panel-header')

                <main class="user-shell__main">
                    <div class="w-full max-w-6xl mx-auto user-shell__container px-3 sm:px-4 py-4 md:py-5">
                        <div class="mb-3">
                            @include('layouts.partials.breadcrumbs', ['items' => $breadcrumbs])
                        </div>
                        @if ($header)
                            <div class="user-page-header mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div class="min-w-0">
                                    <h1 class="text-2xl font-semibold tracking-tight mb-1">{{ $header }}</h1>
                                    @if ($subheader)
                                        <p class="text-sm text-muted-foreground mb-0">{{ $subheader }}</p>
                                    @endif
                                </div>
                                @if ($headerActions)
                                    <div class="user-page-header__actions flex flex-wrap gap-2 w-full md:w-auto">{!! $headerActions !!}</div>
                                @endif
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
