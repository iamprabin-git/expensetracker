<x-user-layout>
    <x-slot name="header">Notifications</x-slot>
    <x-slot name="subheader">All alerts for reminders, account status, and membership.</x-slot>
    <x-slot name="headerActions">
        @if (auth()->user()->unreadNotifications()->exists())
            <form method="POST" action="{{ route('notifications.read-all') }}" id="mark-all-notifications-form">
                @csrf
                <button type="submit" class="btn-secondary-app" onclick="event.preventDefault(); markAllNotificationsRead(this.form);">Mark all as read</button>
            </form>
        @endif
    </x-slot>

    <div class="card-panel p-0 overflow-hidden">
        @forelse ($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = $notification->read_at === null;
            @endphp
            <div @class(['user-notifications__page-item border-bottom p-3', 'user-notifications__page-item--unread' => $isUnread])>
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-pill user-notifications__cat user-notifications__cat--{{ $data['category'] ?? 'general' }}">
                                {{ ucfirst($data['category'] ?? 'general') }}
                            </span>
                            @if ($isUnread)
                                <span class="badge text-bg-primary">New</span>
                            @endif
                        </div>
                        <h2 class="h6 fw-semibold mb-1">{{ $data['title'] ?? 'Notification' }}</h2>
                        <p class="small text-secondary mb-2">{{ $data['message'] ?? '' }}</p>
                        <p class="small text-secondary mb-0">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="d-flex flex-column gap-1 flex-shrink-0">
                        @if (! empty($data['action_url']))
                            <a href="{{ $data['action_url'] }}" class="btn btn-sm btn-outline-primary"
                               @if ($isUnread) data-mark-read-on-click="{{ $notification->id }}" @endif>
                                {{ $data['action_label'] ?? 'View' }}
                            </a>
                        @endif
                        @if ($isUnread)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Mark read</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-secondary py-5 px-3">
                <p class="mb-0">No notifications yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $notifications->links() }}</div>

    @push('scripts')
        <script>
            async function markAllNotificationsRead(form) {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                if (response.ok) window.location.reload();
            }

            document.querySelectorAll('[data-mark-read-on-click]').forEach((link) => {
                link.addEventListener('click', () => {
                    const id = link.dataset.markReadOnClick;
                    fetch(`{{ url('/notifications') }}/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                });
            });
        </script>
    @endpush
</x-user-layout>
