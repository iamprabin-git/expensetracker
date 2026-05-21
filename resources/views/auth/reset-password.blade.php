<x-guest-layout>
    <h1 class="text-xl font-semibold tracking-tight font-bold mb-2">Set a new password</h1>
    <p class="text-sm text-muted-foreground mb-4">Choose a strong password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}" class="grid grid-cols-12 gap-3">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="col-span-12">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="col-span-12">
            <x-input-label for="password" :value="__('New password')" />
            <x-text-input id="password" class="block mt-1 w-full flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="col-span-12">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="col-span-12 flex flex-wrap gap-2 justify-between items-center">
            @include('auth.partials.auth-back-link')
            <x-primary-button class="ms-auto">
                {{ __('Reset password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
