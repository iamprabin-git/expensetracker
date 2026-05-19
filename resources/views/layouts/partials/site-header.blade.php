@php
    $variant = $variant ?? 'marketing';
    $navbarId = $variant === 'app' ? 'appNavbar' : 'siteNavbar';
    $brandUrl = $variant === 'app' ? route('dashboard') : route('home');
@endphp

<header class="site-header" data-site-header>
    <div class="site-header__backdrop" data-site-header-backdrop aria-hidden="true"></div>

    <nav class="site-header__nav" aria-label="Main navigation">
        <div class="container">
            {{-- Top bar --}}
            <div class="site-header__inner">
                <a class="site-brand" href="{{ $brandUrl }}">
                    <span class="site-brand__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797-2.101c6.27 1.645 10.53 4.978 12.453 7.75M2.25 9.75v10.5m0-10.5c0-3.75 3.75-6.75 8.25-6.75s8.25 3 8.25 6.75m-16.5 0v10.5" />
                        </svg>
                    </span>
                    <span class="site-brand__text">Expense<span class="site-brand__accent">Tracker</span></span>
                </a>

                {{-- Desktop nav --}}
                <ul class="site-header__menu site-header__menu--desktop d-none d-lg-flex">
                    @if ($variant === 'app')
                        @include('layouts.partials.site-header-nav-links', ['context' => 'app'])
                    @else
                        @include('layouts.partials.site-header-nav-links', ['context' => 'marketing'])
                    @endif
                </ul>

                <div class="site-header__actions">
                    @include('components.theme-toggle')

                    <div class="site-header__auth d-none d-lg-flex align-items-center gap-2">
                        @auth
                            @include('layouts.partials.site-header-user-menu')
                        @else
                            @include('layouts.partials.site-auth-buttons')
                        @endauth
                    </div>

                    <button
                        type="button"
                        class="site-header__toggle d-lg-none"
                        data-site-header-toggle
                        aria-controls="{{ $navbarId }}"
                        aria-expanded="false"
                        aria-label="Open navigation menu"
                    >
                        <span class="site-header__toggle-box" aria-hidden="true">
                            <span class="site-header__toggle-line"></span>
                            <span class="site-header__toggle-line"></span>
                            <span class="site-header__toggle-line"></span>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Mobile drawer --}}
            <div
                class="site-header__mobile d-lg-none"
                id="{{ $navbarId }}"
                data-site-header-panel
                aria-hidden="true"
            >
                <div class="site-header__mobile-inner">
                    <ul class="site-header__menu site-header__menu--mobile">
                        @if ($variant === 'app')
                            @include('layouts.partials.site-header-nav-links', ['context' => 'app', 'mobile' => true])
                        @else
                            @include('layouts.partials.site-header-nav-links', ['context' => 'marketing', 'mobile' => true])
                        @endif
                    </ul>

                    <div class="site-header__mobile-auth d-lg-none">
                        @auth
                            @include('layouts.partials.site-header-user-menu', ['mobile' => true])
                        @else
                            @include('layouts.partials.site-auth-buttons', ['mobile' => true])
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
