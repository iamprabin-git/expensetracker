@props([
    'id',
    'title',
    'description' => null,
    'danger' => false,
])

<section
    {{ $attributes->class(['settings-panel', 'settings-panel--danger' => $danger]) }}
    id="{{ $id }}"
    aria-labelledby="{{ $id }}-heading"
>
    <header class="settings-panel__header">
        @if (isset($icon))
            <span class="settings-panel__icon" aria-hidden="true">{{ $icon }}</span>
        @endif
        <div class="settings-panel__heading">
            <h2 class="settings-panel__title" id="{{ $id }}-heading">{{ $title }}</h2>
            @if ($description)
                <p class="settings-panel__description">{{ $description }}</p>
            @endif
        </div>
    </header>
    <div class="settings-panel__body">
        {{ $slot }}
    </div>
</section>
