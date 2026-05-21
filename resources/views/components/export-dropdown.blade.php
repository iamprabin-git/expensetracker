@props([
    'pdfHref',
    'csvHref',
    'xlsxHref',
    'label' => 'Download',
])

<div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
    <x-ui.button variant="outline" size="sm" type="button" class="gap-1.5" @click="open = ! open" x-bind:aria-expanded="open">
        {{ $label }}
        <svg class="size-3.5 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </x-ui.button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute end-0 z-50 mt-1 min-w-[10.5rem] origin-top-right rounded-md border border-border bg-popover py-1 shadow-md"
        style="display: none;"
        @click="open = false"
        role="menu"
    >
        <a
            href="{{ $pdfHref }}"
            class="block px-3 py-2 text-sm text-popover-foreground hover:bg-accent hover:text-accent-foreground"
            role="menuitem"
        >PDF</a>
        <a
            href="{{ $csvHref }}"
            class="block px-3 py-2 text-sm text-popover-foreground hover:bg-accent hover:text-accent-foreground"
            role="menuitem"
        >CSV</a>
        <a
            href="{{ $xlsxHref }}"
            class="block px-3 py-2 text-sm text-popover-foreground hover:bg-accent hover:text-accent-foreground"
            role="menuitem"
        >Excel</a>
    </div>
</div>
