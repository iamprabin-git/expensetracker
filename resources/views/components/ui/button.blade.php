@props([
    'variant' => 'default',
    'size' => 'default',
    'type' => 'button',
    'disabled' => false,
    'headerLink' => false,
    'tag' => null,
])

@php
    $base =
        "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]";

    $variants = [
        'default' => 'bg-primary text-primary-foreground hover:bg-primary/90',
        'destructive' =>
            'bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40',
        'outline' =>
            'border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:hover:bg-input/50',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'ghost' => 'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
        'link' => 'text-primary underline-offset-4 hover:underline',
    ];

    $sizes = [
        'default' => 'h-9 px-4 py-2',
        'sm' => 'h-8 rounded-md px-3',
        'lg' => 'h-10 rounded-md px-6',
        'icon' => 'size-9',
    ];

    $class = trim(
        $base .
            ' ' .
            ($variants[$variant] ?? $variants['default']) .
            ' ' .
            ($sizes[$size] ?? $sizes['default']),
    );
@endphp

@php
    $attr = $attributes->merge(['class' => $class]);
    if ($headerLink) {
        $attr = $attr->merge(['data-site-header-link' => true]);
    }

    $resolvedTag = $tag ?? ($attributes->has('href') ? 'a' : 'button');
@endphp

@if ($resolvedTag === 'label')
    <label {{ $attr }}>{{ $slot }}</label>
@elseif ($resolvedTag === 'a' || $attributes->has('href'))
    <a {{ $attr }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attr }} @if ($disabled) disabled @endif>{{ $slot }}</button>
@endif
