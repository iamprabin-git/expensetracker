<div class="dropdown user-notifications" data-user-notifications>
    <button
        type="button"
        class="user-panel-header__icon-btn dropdown-toggle user-notifications__toggle"
        data-bs-toggle="dropdown"
        data-bs-display="static"
        data-notifications-toggle
        aria-expanded="false"
        aria-label="Notifications"
        title="Notifications"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <span class="user-notifications__badge d-none" data-notifications-badge aria-hidden="true">0</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 user-notifications__menu">
        <div class="user-notifications__header d-flex align-items-center justify-content-between gap-2 px-3 py-2 border-bottom">
            <span class="fw-semibold">Notifications</span>
            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none d-none" data-notifications-mark-all>
                Mark all read
            </button>
        </div>

        <div class="user-notifications__list" data-notifications-list role="list">
            <div class="user-notifications__loading px-3 py-4 text-center text-secondary small" data-notifications-loading>
                Loading…
            </div>
        </div>

        <div class="user-notifications__footer border-top px-3 py-2 text-center">
            <a href="{{ route('notifications.index') }}" class="small text-decoration-none">View all notifications</a>
        </div>
    </div>
</div>
