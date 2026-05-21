@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-card text-card-foreground',
        'destructive' =>
            'border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive',
        'success' => 'border-emerald-500/50 text-emerald-950 dark:text-emerald-50 [&>svg]:text-emerald-600',
    ];
@endphp

<div
    role="alert"
    {{ $attributes->merge([
        'class' =>
            'relative w-full rounded-lg border px-4 py-3 text-sm grid has-[>svg]:grid-cols-[calc(var(--spacing)*4)_1fr] grid-cols-[0_1fr] has-[>svg]:gap-x-3 gap-y-0.5 items-start [&>svg]:size-4 [&>svg]:translate-y-0.5 ' .
            ($variants[$variant] ?? $variants['default']),
    ]) }}
>
    {{ $slot }}
</div>
