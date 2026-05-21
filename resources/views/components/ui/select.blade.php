@props(['disabled' => false])

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' =>
            'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30',
    ]) }}
>
    {{ $slot }}
</select>
