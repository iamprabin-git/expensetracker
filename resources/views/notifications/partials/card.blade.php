@php
    $data = $notification->data;
    $category = $data['category'] ?? 'general';
    $isUnread = $notification->read_at === null;
@endphp

<article @class(['user-notifications__card', 'user-notifications__card--unread' => $isUnread])>
    @if ($isUnread)
        <span class="user-notifications__card-indicator" aria-hidden="true"></span>
    @endif

    <div class="user-notifications__card-inner">
        @include('notifications.partials.icon', ['category' => $category])

        <div class="user-notifications__card-body min-w-0 flex-grow-1">
            <div class="user-notifications__card-meta">
                <span class="user-notifications__pill user-notifications__pill--{{ $category }}">
                    {{ $meta['label'] }}
                </span>
                @if ($isUnread)
                    <span class="user-notifications__pill user-notifications__pill--new">Unread</span>
                @endif
                <time class="user-notifications__card-time" datetime="{{ $notification->created_at->toIso8601String() }}">
                    {{ $notification->created_at->diffForHumans() }}
                </time>
            </div>

            <h2 class="user-notifications__card-title">{{ $data['title'] ?? 'Notification' }}</h2>
            <p class="user-notifications__card-message">{{ $data['message'] ?? '' }}</p>
        </div>

        <div class="user-notifications__card-actions">
            @if (! empty($data['action_url']))
                <a
                    href="{{ $data['action_url'] }}"
                    class="btn btn-sm btn-primary user-notifications__btn-action"
                    @if ($isUnread) data-mark-read-on-click="{{ $notification->id }}" @endif
                >
                    {{ $data['action_label'] ?? 'View' }}
                </a>
            @endif
            @if ($isUnread)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary user-notifications__btn-action">
                        Mark read
                    </button>
                </form>
            @endif
            <form
                method="POST"
                action="{{ route('notifications.destroy', $notification->id) }}"
                class="d-inline"
                onsubmit="return confirm('Delete this notification?')"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger user-notifications__btn-action" aria-label="Delete notification">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</article>
