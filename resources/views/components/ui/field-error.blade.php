@props(['messages'])

@if ($messages)
    <p {{ $attributes->merge(['class' => 'mt-1 text-sm text-destructive']) }}>
        {{ is_array($messages) ? $messages[0] : $messages }}
    </p>
@endif
