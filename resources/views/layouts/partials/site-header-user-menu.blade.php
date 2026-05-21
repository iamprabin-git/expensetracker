@php $mobile = $mobile ?? false; @endphp

@if ($mobile)
    <div class="site-header__mobile-user card-panel p-3">
        <div class="flex items-center gap-3 mb-3">
            <x-user-avatar :user="auth()->user()" size="header" />
            <div class="min-w-0">
                <p class="font-semibold mb-0 truncate">{{ auth()->user()->name }}</p>
                <p class="text-sm text-muted-foreground mb-0 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="grid gap-2">
            <x-ui.button variant="outline" size="sm" href="{{ route('dashboard') }}" class="w-full" data-site-header-link>Dashboard</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('settings.index') }}" class="w-full" data-site-header-link>Settings</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('home') }}" class="w-full" data-site-header-link>Website</x-ui.button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button type="submit" variant="destructive" size="sm" class="w-full">Log out</x-ui.button>
            </form>
        </div>
    </div>
@else
    <div class="dropdown">
        <button
            type="button"
            class="site-header__user-btn dropdown-toggle"
            data-bs-toggle="dropdown"
            data-bs-display="static"
            aria-expanded="false"
            aria-label="Account menu"
        >
            <x-user-avatar :user="auth()->user()" size="header" />
        </button>
        <ul class="dropdown-menu dropdown-menu-end site-header__dropdown site-header__dropdown-user py-2">
            <li class="px-3 py-2 border-b border-border mb-1">
                <p class="font-semibold mb-0 text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-muted-foreground mb-0">{{ auth()->user()->email }}</p>
            </li>
            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a class="dropdown-item" href="{{ route('settings.index') }}">Settings</a></li>
            <li><a class="dropdown-item" href="{{ route('home') }}">Website</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-destructive">Log out</button>
                </form>
            </li>
        </ul>
    </div>
@endif
