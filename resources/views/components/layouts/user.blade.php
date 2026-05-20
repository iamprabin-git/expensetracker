@props(['title' => null, 'header' => null, 'subheader' => null, 'headerActions' => null])

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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="user-shell min-h-full" data-user-shell>
        <div class="user-shell__backdrop" data-user-sidebar-backdrop aria-hidden="true"></div>

        <div class="user-shell__layout">
            @include('layouts.partials.user-sidebar')

            <div class="user-shell__content">
                @include('layouts.partials.user-panel-header')

                @if (session('success'))
                    <div class="user-shell__alert px-3 px-lg-4 pt-3">
                        <div class="alert alert-success mb-0 rounded-xl border-0 shadow-sm" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="user-shell__alert px-3 px-lg-4 pt-3">
                        <div class="alert alert-danger mb-0 rounded-xl border-0 shadow-sm" role="alert">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                <main class="user-shell__main">
                    <div class="container-fluid user-shell__container px-3 px-sm-4 py-4 py-md-5">
                        @if ($header)
                            <div class="user-page-header row mb-4">
                                <div class="col-12 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                    <div class="min-w-0">
                                        <h1 class="h3 fw-bold mb-1">{{ $header }}</h1>
                                        @if ($subheader)
                                            <p class="text-secondary mb-0">{{ $subheader }}</p>
                                        @endif
                                    </div>
                                    @if ($headerActions)
                                        <div class="user-page-header__actions d-flex flex-wrap gap-2 w-100 w-md-auto">{!! $headerActions !!}</div>
                                    @endif
                                </div>
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
