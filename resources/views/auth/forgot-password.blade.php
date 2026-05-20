<x-guest-layout>
    <h1 class="h4 fw-bold mb-2">Forgot password</h1>
    <p class="small text-secondary mb-4">
        Enter the email address for your user account. We will send a link to reset your password.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="row g-3">
        @csrf

        <div class="col-12">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 justify-content-between align-items-center">
            @include('auth.partials.auth-back-link')
            <x-primary-button class="ms-auto">
                {{ __('Email reset link') }}
            </x-primary-button>
        </div>
    </form>

    <p class="small text-secondary mt-3 mb-0">
        Admin? Use <a href="{{ url('/admin/password-reset/request') }}" class="text-decoration-none">admin password reset</a>.
    </p>
</x-guest-layout>
