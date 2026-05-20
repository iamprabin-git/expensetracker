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
                <div class="container">
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
            <section class="site-section site-section--muted py-4">
                <div class="container">
                    <div class="row g-4">
                        @foreach ($items as $item)
                            <div class="col-6 col-md-3">
                                <div class="site-stat">
                                    <div class="site-stat__value">{{ $item['value'] ?? '' }}</div>
                                    <div class="site-stat__label">{{ $item['label'] ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('feature_cards')
            <section class="site-section">
                <div class="container">
                    @if (! empty($section['title']))
                        <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
                            <h2 class="site-section-title">{{ $section['title'] }}</h2>
                            @if (! empty($section['subtitle']))
                                <p class="site-section-lead">{{ $section['subtitle'] }}</p>
                            @endif
                        </div>
                    @endif
                    <div class="row g-4">
                        @foreach ($items as $item)
                            @php $itemImage = filled($item['image'] ?? null) ? $page->imageUrl($item['image']) : null; @endphp
                            <div class="col-md-6 col-lg-4">
                                <article class="site-feature-card h-100">
                                    @if ($itemImage)
                                        <img src="{{ $itemImage }}" alt="" class="img-fluid rounded-3 mb-3 w-100" style="height: 10rem; object-fit: cover;">
                                    @elseif (! empty($item['icon']))
                                        <span class="site-feature-card__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                                        </span>
                                    @endif
                                    @if (! empty($item['title']))
                                        <h3 class="h5 fw-semibold mb-2">{{ $item['title'] }}</h3>
                                    @endif
                                    @if (! empty($item['text']))
                                        <p class="text-secondary mb-0 small">{{ $item['text'] }}</p>
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
                <div class="container">
                    @if (! empty($section['title']))
                        <div class="text-center mb-5">
                            <h2 class="site-section-title">{{ $section['title'] }}</h2>
                        </div>
                    @endif
                    <div class="row g-4">
                        @foreach ($items as $index => $item)
                            <div class="col-md-4 text-center">
                                <div class="rounded-circle bg-indigo-600 text-white d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width:3rem;height:3rem;">{{ $index + 1 }}</div>
                                @if (! empty($item['title']))
                                    <h3 class="h5 fw-semibold">{{ $item['title'] }}</h3>
                                @endif
                                @if (! empty($item['text']))
                                    <p class="text-secondary small mb-0">{{ $item['text'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('values')
            <section class="site-section">
                <div class="container">
                    <div class="row g-5 align-items-center">
                        @if ($sectionImage)
                            <div class="col-lg-6 order-lg-2">
                                <img src="{{ $sectionImage }}" alt="" class="img-fluid rounded-4 shadow-sm w-100">
                            </div>
                        @endif
                        <div class="{{ $sectionImage ? 'col-lg-6 order-lg-1' : 'col-12' }}">
                            <div class="card-panel">
                                @if (! empty($section['title']))
                                    <h3 class="h5 fw-semibold mb-3">{{ $section['title'] }}</h3>
                                @endif
                                <ul class="list-unstyled mb-0 d-grid gap-3">
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
                <div class="container">
                    <div class="row g-5 align-items-center">
                        <div class="col-lg-6">
                            @if (! empty($section['title']))
                                <h2 class="site-section-title h3">{{ $section['title'] }}</h2>
                            @endif
                            @foreach ($items as $item)
                                @if (! empty($item['text']))
                                    <p class="text-secondary {{ $loop->last ? 'mb-0' : '' }}">{{ $item['text'] }}</p>
                                @endif
                            @endforeach
                        </div>
                        <div class="col-lg-6">
                            @if ($sectionImage)
                                <img src="{{ $sectionImage }}" alt="" class="img-fluid rounded-4 shadow-sm w-100">
                            @else
                                <div class="card-panel h-100 d-flex align-items-center justify-content-center text-secondary small">
                                    Upload a section image in admin
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            @break

        @case('pricing')
            <section class="site-section">
                <div class="container">
                    <div class="row g-4 justify-content-center align-items-stretch">
                        @foreach ($items as $item)
                            @php
                                $featured = ! empty($item['featured']);
                                $features = array_filter(explode("\n", $item['features'] ?? ''));
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="site-pricing-card {{ $featured ? 'site-pricing-card--featured' : '' }} h-100 position-relative">
                                    @if (! empty($item['badge']))
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-3">{{ $item['badge'] }}</span>
                                    @endif
                                    @if (! empty($item['image']))
                                        <img src="{{ $page->imageUrl($item['image']) }}" alt="" class="img-fluid rounded-3 mb-3">
                                    @endif
                                    <h3 class="h5 fw-bold">{{ $item['title'] ?? '' }}</h3>
                                    <p class="display-6 fw-bold my-3">{{ $item['price'] ?? '' }}<span class="fs-6 fw-normal text-secondary">{{ $item['period'] ?? '' }}</span></p>
                                    <ul class="list-unstyled small text-secondary flex-grow-1 mb-4">
                                        @foreach ($features as $feature)
                                            <li class="mb-2">✓ {{ trim($feature) }}</li>
                                        @endforeach
                                    </ul>
                                    @if (! empty($item['link_label']))
                                        <a href="{{ CmsUrl::resolve($item['link_url'] ?? '#') }}" class="btn {{ $featured ? 'btn-primary site-btn-primary' : 'btn-outline-primary' }} w-100">{{ $item['link_label'] }}</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('faq')
            <section class="site-section pt-0">
                <div class="container" style="max-width: 48rem;">
                    <div class="accordion" id="faqAccordion">
                        @foreach ($items as $i => $item)
                            <div class="site-faq-item mb-2 overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                        {{ $item['title'] ?? '' }}
                                    </button>
                                </h2>
                                <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">{{ $item['text'] ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
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
                <div class="container">
                    <div class="site-cta">
                        @if (! empty($section['title']))
                            <h2 class="display-6 fw-bold mb-3">{{ $section['title'] }}</h2>
                        @endif
                        @if (! empty($section['subtitle']))
                            <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 32rem;">{{ $section['subtitle'] }}</p>
                        @endif
                        @foreach ($items as $item)
                            @if (! empty($item['link_label']))
                                <a href="{{ CmsUrl::resolve($item['link_url'] ?? '/register') }}" class="btn btn-light btn-lg fw-semibold px-5">{{ $item['link_label'] }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
            @break
    @endswitch
@endforeach
