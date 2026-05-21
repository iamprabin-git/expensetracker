@props(['disabled' => false])

<input
    type="checkbox"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' =>
            'size-4 shrink-0 rounded border border-input shadow-xs transition-shadow outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 text-primary',
    ]) }}
/>
