@php
    $author = $review->user;
    $displayName = $review->display_name;
    $initials = $author
        ? $author->initials()
        : strtoupper(collect(preg_split('/\s+/', trim($displayName)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn (string $part) => substr($part, 0, 1))
            ->join('') ?: substr($displayName, 0, 2));
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3 min-w-0']) }}>
    @if ($author?->profilePhotoUrl())
        <img
            src="{{ $author->profilePhotoUrl() }}"
            alt="{{ $displayName }}"
            class="user-avatar user-avatar--img user-avatar--lg shrink-0"
            width="48"
            height="48"
            loading="lazy"
            referrerpolicy="no-referrer"
        >
    @else
        <span class="user-avatar user-avatar--lg shrink-0" aria-hidden="true">{{ $initials }}</span>
    @endif

    <div class="min-w-0">
        <p class="truncate font-semibold text-foreground">{{ $displayName }}</p>
        <p class="text-xs text-muted-foreground">
            @if ($author?->google_id)
                Google member
                <span class="mx-1 opacity-50" aria-hidden="true">·</span>
            @endif
            {{ $review->approved_at?->format('F j, Y') }}
        </p>
    </div>
</div>
