<x-auth-layout title="Log in" mode="login">
    <header class="auth-card__head">
        <h1 class="auth-card__title">Welcome back</h1>
        <p class="auth-card__subtitle">Sign in to manage transactions, budgets, and reports.</p>
    </header>

    @if (session('status'))
        <div class="auth-alert auth-alert--info" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
        <div class="auth-alert auth-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    @include('auth.partials.google-button')

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="auth-form__field">
            <x-ui.label for="email">{{ __('Email address') }}</x-ui.label>
            <x-ui.input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
                class="auth-input"
                @class(['aria-invalid' => $errors->has('email')])
            />
            <x-ui.field-error :messages="$errors->get('email')" />
        </div>

        <div class="auth-form__field">
            <x-ui.label for="password">{{ __('Password') }}</x-ui.label>
            <x-ui.input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="auth-input"
                @class(['aria-invalid' => $errors->has('password')])
            />
            <x-ui.field-error :messages="$errors->get('password')" />
        </div>

        <div class="auth-form__row">
            <label for="remember_me" class="auth-form__remember">
                <x-ui.checkbox id="remember_me" name="remember" />
                <span>{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="auth-form__link" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <x-ui.button type="submit" size="lg" class="auth-form__submit">{{ __('Sign in') }}</x-ui.button>
    </form>

    <p class="auth-card__switch">
        {{ __('Don\'t have an account?') }}
        <a href="{{ route('register') }}">{{ __('Create account') }}</a>
    </p>

    <div class="auth-card__note">
        <p class="mb-0">Accounts are approved by an administrator before dashboard access.</p>
        <div class="auth-card__note-links">
            <a href="{{ url('/admin/login') }}">Admin login</a>
            <span aria-hidden="true">·</span>
            <a href="{{ url('/admin/password-reset/request') }}">Admin password reset</a>
        </div>
    </div>
</x-auth-layout>
