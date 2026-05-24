<x-auth-layout title="Create account" mode="register">
    <header class="auth-card__head">
        <h1 class="auth-card__title">Create your account</h1>
        <p class="auth-card__subtitle">Join free — start tracking your money in minutes.</p>
    </header>

    <div class="auth-alert auth-alert--info" role="status">
        <strong>What happens next:</strong> we email the admin for approval and send you login details. Sign in once approved.
    </div>

    @if ($errors->any() && ! $errors->has('name') && ! $errors->has('email') && ! $errors->has('password') && ! $errors->has('password_confirmation'))
        <div class="auth-alert auth-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    @include('auth.partials.google-button')

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="auth-form__grid">
            <div class="auth-form__field auth-form__field--full">
                <x-ui.label for="name">{{ __('Full name') }}</x-ui.label>
                <x-ui.input
                    id="name"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Jane Doe"
                    class="auth-input"
                    @class(['aria-invalid' => $errors->has('name')])
                />
                <x-ui.field-error :messages="$errors->get('name')" />
            </div>

            <div class="auth-form__field auth-form__field--full">
                <x-ui.label for="email">{{ __('Email address') }}</x-ui.label>
                <x-ui.input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
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
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="auth-input"
                    @class(['aria-invalid' => $errors->has('password')])
                />
                <x-ui.field-error :messages="$errors->get('password')" />
            </div>

            <div class="auth-form__field">
                <x-ui.label for="password_confirmation">{{ __('Confirm') }}</x-ui.label>
                <x-ui.input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="auth-input"
                    @class(['aria-invalid' => $errors->has('password_confirmation')])
                />
                <x-ui.field-error :messages="$errors->get('password_confirmation')" />
            </div>
        </div>

        <x-ui.button type="submit" size="lg" class="auth-form__submit">{{ __('Create account') }}</x-ui.button>
    </form>

    <p class="auth-card__switch">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
    </p>

    <div class="auth-card__note">
        <p class="mb-0">New accounts require admin approval before dashboard access.</p>
        <div class="auth-card__note-links">
            <a href="{{ url('/admin/login') }}">Admin login</a>
        </div>
    </div>
</x-auth-layout>
