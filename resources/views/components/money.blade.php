@props(['amount', 'user' => null])

@php
    $user = $user ?? auth()->user();
    $formatted = $user
        ? $user->formatMoney((float) $amount)
        : '$'.number_format((float) $amount, 2);
@endphp

<span {{ $attributes }}>{{ $formatted }}</span>
