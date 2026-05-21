<div class="relative w-full overflow-x-auto table-scroll-touch">
    <table {{ $attributes->merge(['class' => 'w-full caption-bottom text-sm']) }}>
        {{ $slot }}
    </table>
</div>
