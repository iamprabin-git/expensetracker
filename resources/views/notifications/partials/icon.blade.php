@php
    $category = $category ?? 'general';
@endphp
@include('notifications.partials.category-meta')
<span class="fb-notif-row__icon fb-notif-row__icon--{{ $category }}" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] ?? '' }}" />
    </svg>
</span>
