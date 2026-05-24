<nav class="auth-tabs" aria-label="Authentication">
    <a
        href="{{ route('login') }}"
        @class(['auth-tabs__link', 'auth-tabs__link--active' => request()->routeIs('login')])
    >
        {{ __('Sign in') }}
    </a>
    <a
        href="{{ route('register') }}"
        @class(['auth-tabs__link', 'auth-tabs__link--active' => request()->routeIs('register')])
    >
        {{ __('Sign up') }}
    </a>
</nav>
