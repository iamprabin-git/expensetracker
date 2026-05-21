@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border bg-card text-card-foreground shadow-sm']) }}>
    @if ($title || $description || isset($header))
        <div class="flex flex-col gap-1.5 p-6 pb-0">
            @isset($header)
                {{ $header }}
            @else
                @if ($title)
                    <div class="font-semibold leading-none tracking-tight">{{ $title }}</div>
                @endif
                @if ($description)
                    <p class="text-sm text-muted-foreground">{{ $description }}</p>
                @endif
            @endisset
        </div>
    @endif

    <div @class(['p-6', 'pt-6' => ! $title && ! $description && ! isset($header), 'pt-0' => $title || $description || isset($header)])>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="flex items-center border-t p-6 pt-0">{{ $footer }}</div>
    @endisset
</div>
