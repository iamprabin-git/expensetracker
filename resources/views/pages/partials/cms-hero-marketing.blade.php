@props(['page'])

@php
    $hasHeroImage = (bool) $page->heroImageUrl();
    $bullets = collect($page->sectionList())->firstWhere('type', 'hero_bullets');
@endphp

<section class="site-hero site-section pb-0">
    <div class="site-hero__grid" aria-hidden="true"></div>
    <div class="mx-auto w-full max-w-6xl px-4 position-relative py-5 py-lg-6">
        <div class="grid grid-cols-12 gap-4 items-center g-5 py-4 py-lg-5">
            <div @class(['col-span-12', 'lg:col-span-6' => $hasHeroImage, 'lg:col-span-12' => ! $hasHeroImage])>
                @if ($page->hero_badge)
                    <span class="badge bg-white/15 text-white border border-white/25 mb-3 px-3 py-2">{{ $page->hero_badge }}</span>
                @endif
                @if ($page->hero_title)
                    <h1 class="display-4 font-bold mb-4 lh-sm site-hero__title">{{ $page->hero_title }}</h1>
                @endif
                @if ($page->hero_lead)
                    <p class="site-hero__lead text-lg text-black mb-4 pe-lg-4">{{ $page->hero_lead }}</p>
                @endif
                @if ($page->extra('primary_cta_label') || $page->extra('secondary_cta_label'))
                    <div class="flex flex-wrap gap-3">
                        @if ($page->extra('primary_cta_label'))
                            <a href="{{ \App\Support\CmsUrl::resolve($page->extra('primary_cta_url', '/register')) }}" class="btn btn-light btn-lg font-semibold px-4">{{ $page->extra('primary_cta_label') }}</a>
                        @endif
                        @if ($page->extra('secondary_cta_label'))
                            <a href="{{ \App\Support\CmsUrl::resolve($page->extra('secondary_cta_url', '/features')) }}" class="btn btn-outline-light btn-lg px-4">{{ $page->extra('secondary_cta_label') }}</a>
                        @endif
                    </div>
                @endif
                @if ($page->extra('hero_note'))
                    <p class="text-sm text-indigo-200 mt-3 mb-0">{{ $page->extra('hero_note') }}</p>
                @endif
            </div>
            @if ($hasHeroImage)
                <div class="col-span-12 lg:col-span-6">
                    <img src="{{ $page->heroImageUrl() }}" alt="" class="img-fluid rounded-4 shadow-lg w-full">
                </div>
            @endif
        </div>

        @if ($bullets && count($bullets['items'] ?? []) > 0)
            <div class="site-hero__trust mt-8 border-t border-white/20 pt-6">
                @if (! empty($bullets['title']))
                    <p class="mb-4 text-center text-sm font-medium text-indigo-100">{{ $bullets['title'] }}</p>
                @endif
                <ul class="site-hero__trust-list mb-0 list-none">
                    @foreach ($bullets['items'] ?? [] as $item)
                        <li class="site-hero__trust-item">
                            <span class="site-hero__trust-icon" aria-hidden="true">✓</span>
                            <span class="text-sm leading-snug text-white/95">{{ $item['text'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
