@php
    $data = $notification->data;
    $category = $data['category'] ?? 'general';
    $isUnread = $notification->read_at === null;
    $actionUrl = $data['action_url'] ?? null;
    $title = $data['title'] ?? 'Notification';
    $message = $data['message'] ?? '';
@endphp

<div @class(['fb-notif-row', 'fb-notif-row--unread' => $isUnread]) data-notification-id="{{ $notification->id }}">
    @if ($actionUrl)
        <a href="{{ $actionUrl }}" class="fb-notif-row__link" @if ($isUnread) data-mark-read-on-click="{{ $notification->id }}" @endif aria-label="{{ $title }}"></a>
    @endif

    @include('notifications.partials.icon', ['category' => $category])

    <div class="fb-notif-row__body">
        <p @class(['fb-notif-row__copy', 'fb-notif-row__copy--unread' => $isUnread])>
            @if ($isUnread)
                <strong>{{ $title }}</strong>
            @else
                <span class="fb-notif-row__title">{{ $title }}</span>
            @endif
            @if ($message)
                <span class="fb-notif-row__message">{{ $message }}</span>
            @endif
            <span class="fb-notif-row__time"> · {{ $notification->created_at->diffForHumans(null, true) }}</span>
        </p>
    </div>

    @if ($isUnread)
        <span class="fb-notif-row__dot" aria-label="Unread"></span>
    @endif

    <div class="fb-notif-row__actions">
        @if ($isUnread)
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="fb-notif-row__action-form">
                @csrf
                <button type="submit" class="fb-notif-row__action-btn" title="Mark as read" aria-label="Mark as read">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="fb-notif-row__action-form" onsubmit="return confirm('Delete this notification?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="fb-notif-row__action-btn fb-notif-row__action-btn--danger" title="Delete" aria-label="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </form>
    </div>
</div>
