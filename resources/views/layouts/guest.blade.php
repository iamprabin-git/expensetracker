<x-marketing-layout :title="$title ?? null" narrow>
    <div class="container px-3 px-sm-4 w-100">
        <div class="site-auth-card">
            {{ $slot }}
        </div>
    </div>
</x-marketing-layout>
