<x-layouts.user
    :header="$header ?? null"
    :subheader="$subheader ?? null"
    :headerActions="$headerActions ?? null"
    :breadcrumbs="$breadcrumbs ?? null"
>
    {{ $slot }}
</x-layouts.user>
