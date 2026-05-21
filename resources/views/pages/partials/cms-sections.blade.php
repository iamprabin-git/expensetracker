@props(['page', 'reviews' => collect()])

@php
    use App\Support\CmsUrl;
    $sections = collect($page->sectionList())->reject(fn ($s) => ($s['type'] ?? '') === 'hero_bullets');
@endphp

@foreach ($sections as $section)
    @php
        $type = $section['type'] ?? '';
        $items = $section['items'] ?? [];
        $sectionImage = filled($section['image'] ?? null) ? $page->imageUrl($section['image']) : null;
    @endphp

    @switch($type)
        @case('section_header')
            <section class="site-section {{ $loop->first ? '' : '' }}">
                <div class="mx-auto w-full max-w-6xl px-4">
                    <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
                        @if (! empty($section['title']))
                            <h2 class="site-section-title">{{ $section['title'] }}</h2>
                        @endif
                        @if (! empty($section['subtitle']))
                            <p class="site-section-lead">{{ $section['subtitle'] }}</p>
                        @endif
                    </div>
                </div>
            </section>
            @break

        @case('stats')
            <section class="home-stats-bar {{ $page->slug === 'home' ? 'home-stats-bar--below-hero' : 'site-section site-section--muted py-4' }}">
                <div class="mx-auto w-full max-w-6xl px-4">
                    <div class="home-stats-bar__grid">
                        @foreach ($items as $item)
                            <div class="home-stats-bar__item site-stat">
                                <div class="site-stat__value">{{ $item['value'] ?? '' }}</div>
                                <div class="site-stat__label">{{ $item['label'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('feature_cards')
            <section class="site-section">
                <div class="mx-auto w-full max-w-6xl px-4">
                    @if (! empty($section['title']))
                        <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
                            <h2 class="site-section-title">{{ $section['title'] }}</h2>
                            @if (! empty($section['subtitle']))
                                <p class="site-section-lead">{{ $section['subtitle'] }}</p>
                            @endif
                        </div>
                    @endif
                    <div class="grid grid-cols-12 gap-4">
                        @foreach ($items as $item)
                            @php $itemImage = filled($item['image'] ?? null) ? $page->imageUrl($item['image']) : null; @endphp
                            <div class="col-span-12 md:col-span-6 col-lg-4">
                                <article class="site-feature-card h-full">
                                    @if ($itemImage)
                                        <img src="{{ $itemImage }}" alt="" class="img-fluid rounded-3 mb-3 w-full" style="height: 10rem; object-fit: cover;">
                                    @elseif (! empty($item['icon']))
                                        <span class="site-feature-card__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                                        </span>
                                    @endif
                                    @if (! empty($item['title']))
                                        <h3 class="text-lg font-semibold mb-2">{{ $item['title'] }}</h3>
                                    @endif
                                    @if (! empty($item['text']))
                                        <p class="text-muted-foreground mb-0 small">{{ $item['text'] }}</p>
                                    @endif
                                </article>
                            </div>
                        @endforeach
                    </div>
                    @if ($page->slug === 'home')
                        <div class="text-center mt-5">
                            <a href="{{ route('features') }}" class="btn btn-outline-primary btn-lg">View all features</a>
                        </div>
                    @endif
                </div>
            </section>
            @break

        @case('steps')
            <section class="site-section site-section--muted">
                <div class="mx-auto w-full max-w-6xl px-4">
                    @if (! empty($section['title']))
                        <div class="text-center mb-5">
                            <h2 class="site-section-title">{{ $section['title'] }}</h2>
                        </div>
                    @endif
                    <div class="grid grid-cols-12 gap-4">
                        @foreach ($items as $index => $item)
                            <div class="col-span-12 md:col-span-4 text-center">
                                <div class="rounded-circle bg-indigo-600 text-white inline-flex items-center justify-center font-bold mb-3" style="width:3rem;height:3rem;">{{ $index + 1 }}</div>
                                @if (! empty($item['title']))
                                    <h3 class="text-lg font-semibold">{{ $item['title'] }}</h3>
                                @endif
                                @if (! empty($item['text']))
                                    <p class="text-muted-foreground small mb-0">{{ $item['text'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('values')
            <section class="site-section">
                <div class="mx-auto w-full max-w-6xl px-4">
                    <div class="grid grid-cols-12 gap-4 g-5 items-center">
                        @if ($sectionImage)
                            <div class="col-span-12 lg:col-span-6 order-lg-2">
                                <img src="{{ $sectionImage }}" alt="" class="img-fluid rounded-4 shadow-sm w-full">
                            </div>
                        @endif
                        <div class="{{ $sectionImage ? 'col-span-12 lg:col-span-6 order-lg-1' : 'col-span-12' }}">
                            <div class="card-panel">
                                @if (! empty($section['title']))
                                    <h3 class="text-lg font-semibold mb-3">{{ $section['title'] }}</h3>
                                @endif
                                <ul class="list-none mb-0 grid gap-3">
                                    @foreach ($items as $item)
                                        <li>
                                            @if (! empty($item['title']))
                                                <strong class="text-indigo-600 dark:text-indigo-400">{{ $item['title'] }}</strong>
                                            @endif
                                            @if (! empty($item['text']))
                                                — {{ $item['text'] }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @break

        @case('image_text')
            <section class="site-section">
                <div class="mx-auto w-full max-w-6xl px-4">
                    <div class="grid grid-cols-12 gap-4 g-5 items-center">
                        <div class="col-span-12 lg:col-span-6">
                            @if (! empty($section['title']))
                                <h2 class="site-section-title h3">{{ $section['title'] }}</h2>
                            @endif
                            @foreach ($items as $item)
                                @if (! empty($item['text']))
                                    <p class="text-muted-foreground {{ $loop->last ? 'mb-0' : '' }}">{{ $item['text'] }}</p>
                                @endif
                            @endforeach
                        </div>
                        <div class="col-span-12 lg:col-span-6">
                            @if ($sectionImage)
                                <img src="{{ $sectionImage }}" alt="" class="img-fluid rounded-4 shadow-sm w-full">
                            @else
                                <div class="card-panel h-full flex items-center justify-center text-muted-foreground small">
                                    Upload a section image in admin
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            @break

        @case('pricing')
            @include('pages.partials.pricing-section', [
                'page' => $page,
                'items' => $items,
                'title' => $section['title'] ?? null,
                'subtitle' => $section['subtitle'] ?? null,
            ])
            @break

        @case('faq')
            @include('pages.partials.faq-section', [
                'items' => $items,
                'title' => $section['title'] ?? null,
                'subtitle' => $section['subtitle'] ?? null,
            ])
            @break

        @case('contact_info')
            @break

        @case('reviews')
            @include('pages.partials.reviews-section', [
                'reviews' => $reviews,
                'sectionTitle' => $section['title'] ?? null,
                'sectionSubtitle' => $section['subtitle'] ?? null,
            ])
            @break

        @case('cta')
            <section class="site-section">
                <div class="mx-auto w-full max-w-6xl px-4">
                    <div class="site-cta">
                        @if (! empty($section['title']))
                            <h2 class="display-6 font-bold mb-3">{{ $section['title'] }}</h2>
                        @endif
                        @if (! empty($section['subtitle']))
                            <p class="text-lg text-muted-foreground mb-4 opacity-90 mx-auto" style="max-width: 32rem;">{{ $section['subtitle'] }}</p>
                        @endif
                        @foreach ($items as $item)
                            @if (! empty($item['link_label']))
                                <a href="{{ CmsUrl::resolve($item['link_url'] ?? '/register') }}" class="btn btn-light btn-lg font-semibold px-5">{{ $item['link_label'] }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
            @break
    @endswitch
@endforeach
