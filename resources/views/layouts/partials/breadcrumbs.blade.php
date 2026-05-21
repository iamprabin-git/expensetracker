@php
    use App\Support\Breadcrumbs;

    $breadcrumbItems = $items ?? ($breadcrumbs ?? Breadcrumbs::resolve());
@endphp

<x-ui.breadcrumb :items="$breadcrumbItems" />
