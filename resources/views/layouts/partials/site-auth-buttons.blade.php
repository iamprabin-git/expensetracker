@php $mobile = $mobile ?? false; @endphp

<div @class(['site-header__auth-group', 'site-header__auth-group--mobile' => $mobile])>
    @auth
        <x-ui.button
            :href="auth()->user()->isAdmin() ? url('/admin') : route('dashboard')"
            :size="$mobile ? 'default' : 'sm'"
            :class="$mobile ? 'w-full' : ''"
            :header-link="$mobile"
        >
            Dashboard
        </x-ui.button>
    @else
        <x-ui.button
            variant="outline"
            :href="route('login')"
            :size="$mobile ? 'default' : 'sm'"
            :class="$mobile ? 'w-full' : ''"
            :header-link="$mobile"
        >
            Log in
        </x-ui.button>
        <x-ui.button
            :href="route('register')"
            :size="$mobile ? 'default' : 'sm'"
            :class="$mobile ? 'w-full' : ''"
            :header-link="$mobile"
        >
            Sign up free
        </x-ui.button>
    @endauth
</div>
