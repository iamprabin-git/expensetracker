@php
    $mobile = $mobile ?? false;
    $user = Auth::user();
@endphp

@if ($mobile)
    <div class="site-header__mobile-user card-panel p-3">
        <div class="d-flex align-items-center gap-3 mb-3">
            <x-user-avatar :user="$user" size="lg" />
            <div class="min-w-0">
                <p class="fw-semibold mb-0 text-truncate">{{ $user->name }}</p>
                <p class="small text-secondary mb-0 text-truncate">{{ $user->email }}</p>
            </div>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-sm site-btn-outline w-100" data-site-header-link>Dashboard</a>
            <a href="{{ route('settings.index') }}" class="btn btn-sm site-btn-outline w-100" data-site-header-link>Settings</a>
            <a href="{{ route('home') }}" class="btn btn-sm site-btn-outline w-100" data-site-header-link>Website</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Log out</button>
            </form>
        </div>
    </div>
@else
    <div class="dropdown site-header__user">
        <button
            type="button"
            class="site-header__user-btn dropdown-toggle"
            data-bs-toggle="dropdown"
            data-bs-display="static"
            aria-expanded="false"
            aria-label="Account menu for {{ $user->name }}"
            title="{{ $user->name }}"
        >
            <x-user-avatar :user="$user" size="header" />
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 site-header__dropdown">
            <li class="site-header__dropdown-user px-3 py-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <x-user-avatar :user="$user" size="md" />
                    <div class="min-w-0">
                        <p class="fw-semibold mb-0 text-truncate text-slate-900 dark:text-white">{{ $user->name }}</p>
                        <p class="small text-secondary mb-0 text-truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </li>
            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a class="dropdown-item" href="{{ route('settings.index') }}">Settings</a></li>
            <li><a class="dropdown-item" href="{{ route('home') }}">Website</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">Log out</button>
                </form>
            </li>
        </ul>
    </div>
@endif
