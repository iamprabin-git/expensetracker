<x-guest-layout>
    <h1 class="text-xl font-semibold tracking-tight font-bold mb-2">Forgot password</h1>
    <p class="text-sm text-muted-foreground mb-4">
        Enter the email address for your user account. We will send a link to reset your password.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="grid grid-cols-12 gap-3">
        @csrf

        <div class="col-span-12">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="col-span-12 flex flex-wrap gap-2 justify-between items-center">
            @include('auth.partials.auth-back-link')
            <x-primary-button class="ms-auto">
                {{ __('Email reset link') }}
            </x-primary-button>
        </div>
    </form>

    <p class="text-sm text-muted-foreground mt-3 mb-0">
        Admin? Use <a href="{{ url('/admin/password-reset/request') }}" class="no-underline">admin password reset</a>.
    </p>
</x-guest-layout>
