<header class="user-panel-header">
    <div class="user-panel-header__inner">
        <div class="user-panel-header__start">
            <button
                type="button"
                class="user-shell__menu-btn lg:hidden"
                data-user-sidebar-toggle
                aria-expanded="false"
                aria-controls="userSidebar"
                aria-label="Open navigation menu"
            >
                <span class="user-shell__menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>

        <div class="user-panel-header__actions">
            @include('components.theme-toggle')

            @include('layouts.partials.user-panel-notifications')

            <a href="{{ route('home') }}" class="user-panel-header__icon-btn user-panel-header__icon-btn--optional hidden sm:inline-flex" title="View website" aria-label="View website">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A8.966 8.966 0 013 12c0-1.264.26-2.466.732-3.553" />
                </svg>
            </a>

            @include('layouts.partials.user-panel-user-menu')
        </div>
    </div>
</header>
