@php
    $contactInfo = collect($page->sectionList())->firstWhere('type', 'contact_info');
    $contactItems = collect($contactInfo['items'] ?? []);

    if ($company?->email && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Email')) {
        $contactItems->prepend(['title' => 'Email', 'text' => $company->email]);
    }
    if ($company?->phone && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Phone')) {
        $contactItems->push(['title' => 'Phone', 'text' => $company->phone]);
    }
    if ($company?->support_hours && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Hours')) {
        $contactItems->push(['title' => 'Hours', 'text' => $company->support_hours]);
    }
    if ($company?->formattedAddress() && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Address')) {
        $contactItems->push(['title' => 'Address', 'text' => $company->formattedAddress()]);
    }
@endphp

<x-marketing-layout :title="$page->title" :metaDescription="$page->meta_description">
    @include('pages.partials.cms-hero', ['page' => $page])

    @include('pages.partials.contact-section', [
        'page' => $page,
        'contactItems' => $contactItems,
        'company' => $company ?? null,
    ])
</x-marketing-layout>
