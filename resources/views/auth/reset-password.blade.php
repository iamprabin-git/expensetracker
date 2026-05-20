<x-guest-layout>
    <h1 class="h4 fw-bold mb-2">Set a new password</h1>
    <p class="small text-secondary mb-4">Choose a strong password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}" class="row g-3">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="col-12">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full form-control" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="col-12">
            <x-input-label for="password" :value="__('New password')" />
            <x-text-input id="password" class="block mt-1 w-full form-control" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="col-12">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 justify-content-between align-items-center">
            @include('auth.partials.auth-back-link')
            <x-primary-button class="ms-auto">
                {{ __('Reset password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
