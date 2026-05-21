@props(['page'])

@php
    $marketingHeroSlugs = ['home', 'about', 'faq', 'contact', 'pricing'];
@endphp

@if ($page->hero_title || $page->hero_badge || $page->hero_lead)
    @if (in_array($page->slug, $marketingHeroSlugs, true))
        @include('pages.partials.cms-hero-marketing', ['page' => $page])
    @else
        @include('pages.partials.page-hero', [
            'badge' => $page->hero_badge,
            'title' => $page->hero_title,
            'lead' => $page->hero_lead,
            'image' => $page->heroImageUrl(),
        ])
    @endif
@endif
