@props([
    'user' => null,
    'size' => 'md',
])

@php
    $user = $user ?? auth()->user();
    $sizeClass = match ($size) {
        'sm' => 'user-avatar--sm',
        'header' => 'user-avatar--header',
        'lg' => 'user-avatar--lg',
        default => '',
    };
@endphp

@if ($user?->avatarUrl())
    <img
        src="{{ $user->avatarUrl() }}"
        alt="{{ $user->name }}"
        {{ $attributes->class(['user-avatar', 'user-avatar--img', $sizeClass]) }}
    >
@else
    <span {{ $attributes->class(['user-avatar', $sizeClass]) }} aria-hidden="true">{{ $user?->initials() }}</span>
@endif
