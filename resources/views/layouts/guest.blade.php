<x-marketing-layout :title="$title ?? null" narrow>
    <div class="mx-auto w-full max-w-lg px-4">
        <x-ui.card class="site-auth-card">
            {{ $slot }}
        </x-ui.card>
    </div>
</x-marketing-layout>
