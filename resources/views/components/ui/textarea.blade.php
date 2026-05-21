@props(['disabled' => false, 'rows' => 3])

<textarea
    rows="{{ $rows }}"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' =>
            'flex min-h-[4.5rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30',
    ]) }}
>{{ $slot }}</textarea>
