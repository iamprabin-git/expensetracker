@props(['page'])

@if ($page->hero_title || $page->hero_badge || $page->hero_lead)
    @if (request()->routeIs('home'))
        <section class="site-hero site-section pb-0">
            <div class="site-hero__grid" aria-hidden="true"></div>
            <div class="container position-relative py-5 py-lg-6">
                <div class="row align-items-center g-5 py-4 py-lg-5">
                    <div class="col-12 col-lg-6">
                        @if ($page->hero_badge)
                            <span class="badge bg-white/15 text-white border border-white/25 mb-3 px-3 py-2">{{ $page->hero_badge }}</span>
                        @endif
                        @if ($page->hero_title)
                            <h1 class="display-4 fw-bold mb-4 lh-sm site-hero__title">{{ $page->hero_title }}</h1>
                        @endif
                        @if ($page->hero_lead)
                            <p class="lead text-indigo-100 mb-4 pe-lg-4">{{ $page->hero_lead }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-3">
                            @if ($page->extra('primary_cta_label'))
                                <a href="{{ \App\Support\CmsUrl::resolve($page->extra('primary_cta_url', '/register')) }}" class="btn btn-light btn-lg fw-semibold px-4">{{ $page->extra('primary_cta_label') }}</a>
                            @endif
                            @if ($page->extra('secondary_cta_label'))
                                <a href="{{ \App\Support\CmsUrl::resolve($page->extra('secondary_cta_url', '/features')) }}" class="btn btn-outline-light btn-lg px-4">{{ $page->extra('secondary_cta_label') }}</a>
                            @endif
                        </div>
                        @if ($page->extra('hero_note'))
                            <p class="small text-indigo-200 mt-3 mb-0">{{ $page->extra('hero_note') }}</p>
                        @endif
                    </div>
                    <div class="col-12 col-lg-6">
                        @if ($page->heroImageUrl())
                            <img src="{{ $page->heroImageUrl() }}" alt="" class="img-fluid rounded-4 shadow-lg mb-4">
                        @endif
                        @php $bullets = collect($page->sectionList())->firstWhere('type', 'hero_bullets'); @endphp
                        @if ($bullets)
                            <div class="site-hero__preview rounded-4 border border-white/20 bg-white/10 p-4 p-md-5 backdrop-blur shadow-lg">
                                @if (! empty($bullets['title']))
                                    <p class="small text-indigo-200 mb-3">{{ $bullets['title'] }}</p>
                                @endif
                                <ul class="list-unstyled mb-0 d-grid gap-2">
                                    @foreach ($bullets['items'] ?? [] as $item)
                                        <li class="d-flex gap-3 align-items-start rounded-3 bg-white/10 p-3">
                                            <span class="badge bg-success rounded-pill">✓</span>
                                            <span class="small">{{ $item['text'] ?? '' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @else
        @include('pages.partials.page-hero', [
            'badge' => $page->hero_badge,
            'title' => $page->hero_title,
            'lead' => $page->hero_lead,
            'image' => $page->heroImageUrl(),
        ])
    @endif
@endif
