<x-user-layout>
    <x-slot name="header">Notifications</x-slot>
    <x-slot name="subheader"></x-slot>

    <div class="fb-notif-page">
        <div class="fb-notif-page__shell card-panel p-0 overflow-hidden">
            <div class="fb-notif-page__header">
                <h1 class="fb-notif-page__title mb-0">Notifications</h1>
                <div class="fb-notif-page__header-end">
                    @if ($stats['unread'] > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}" id="mark-all-notifications-form" class="mb-0">
                            @csrf
                            <button type="submit" class="fb-notif-page__mark-all" data-notifications-mark-all-btn>
                                Mark all as read
                            </button>
                        </form>
                    @endif
                    <a
                        href="{{ route('dashboard') }}"
                        class="fb-notif-page__close"
                        data-notifications-close
                        aria-label="Close notifications"
                        title="Close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            <nav class="fb-notif-page__tabs" aria-label="Filter notifications">
                <a
                    href="{{ route('notifications.index') }}"
                    @class(['fb-notif-page__tab', 'fb-notif-page__tab--active' => $filter === 'all'])
                >
                    All
                </a>
                <a
                    href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                    @class(['fb-notif-page__tab', 'fb-notif-page__tab--active' => $filter === 'unread'])
                >
                    Unread
                    @if ($stats['unread'] > 0)
                        <span class="fb-notif-page__tab-badge">{{ $stats['unread'] }}</span>
                    @endif
                </a>
            </nav>

            <div class="fb-notif-page__list">
                @forelse ($notifications as $notification)
                    @include('notifications.partials.row', ['notification' => $notification])
                @empty
                    <div class="fb-notif-page__empty">
                        <div class="fb-notif-page__empty-icon" aria-hidden="true">🔔</div>
                        <p class="font-semibold mb-1">
                            @if ($filter === 'unread')
                                You're all caught up
                            @else
                                No notifications yet
                            @endif
                        </p>
                        <p class="text-sm text-muted-foreground mb-0">
                            @if ($filter === 'unread')
                                When you get new alerts, they'll show up here.
                            @else
                                Reminders and account updates will appear here.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        @if ($notifications->hasPages())
            <div class="fb-notif-page__pagination mt-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.querySelector('[data-notifications-close]')?.addEventListener('click', (event) => {
                if (window.history.length > 1 && document.referrer && document.referrer !== window.location.href) {
                    event.preventDefault();
                    window.history.back();
                }
            });

            document.querySelector('[data-notifications-mark-all-btn]')?.addEventListener('click', async (event) => {
                event.preventDefault();
                const form = document.getElementById('mark-all-notifications-form');
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (response.ok) window.location.reload();
            });

            document.querySelectorAll('[data-mark-read-on-click]').forEach((link) => {
                link.addEventListener('click', () => {
                    fetch(`{{ url('/notifications') }}/${link.dataset.markReadOnClick}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                });
            });
        </script>
    @endpush
</x-user-layout>
