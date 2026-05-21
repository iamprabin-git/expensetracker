@props([
    'page',
    'items' => [],
    'title' => null,
    'subtitle' => null,
])

@php
    use App\Support\CmsUrl;

    $plans = collect($items)->filter(fn ($item) => filled($item['title'] ?? null))->values();
@endphp

<section class="pricing-section site-section" id="pricing-plans">
    <div class="mx-auto w-full max-w-6xl px-4">
        <div class="pricing-section__header">
            <h2 class="pricing-section__title">{{ $title ?? 'Choose the plan that fits you' }}</h2>
            <p class="pricing-section__subtitle">
                {{ $subtitle ?? 'Start free and upgrade when you need more power. No hidden fees — cancel anytime.' }}
            </p>
        </div>

        @if ($plans->isEmpty())
            <p class="pricing-section__empty">Pricing plans are not configured yet.</p>
        @else
            <div class="pricing-section__grid">
                @foreach ($plans as $item)
                    @php
                        $featured = ! empty($item['featured']);
                        $features = array_filter(array_map('trim', explode("\n", $item['features'] ?? '')));
                    @endphp
                    <article @class(['pricing-card', 'pricing-card--featured' => $featured])>
                        @if (! empty($item['badge']))
                            <span class="pricing-card__badge">{{ $item['badge'] }}</span>
                        @endif

                        <div class="pricing-card__head">
                            @if (! empty($item['image']))
                                <img
                                    src="{{ $page->imageUrl($item['image']) }}"
                                    alt=""
                                    class="pricing-card__image"
                                >
                            @endif
                            <h3 class="pricing-card__name">{{ $item['title'] ?? '' }}</h3>
                            <p class="pricing-card__price">
                                <span class="pricing-card__amount">{{ $item['price'] ?? '' }}</span>
                                @if (! empty($item['period']))
                                    <span class="pricing-card__period">{{ $item['period'] }}</span>
                                @endif
                            </p>
                        </div>

                        @if ($features !== [])
                            <ul class="pricing-card__features">
                                @foreach ($features as $feature)
                                    <li class="pricing-card__feature">
                                        <span class="pricing-card__check" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-7.5" />
                                            </svg>
                                        </span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (! empty($item['link_label']))
                            <div class="pricing-card__cta">
                                <x-ui.button
                                    :href="CmsUrl::resolve($item['link_url'] ?? '#')"
                                    :variant="$featured ? 'default' : 'outline'"
                                    class="w-full"
                                >
                                    {{ $item['link_label'] }}
                                </x-ui.button>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="pricing-section__footer">
                <div class="pricing-section__note">
                    <p class="pricing-section__note-title">Transparent billing</p>
                    <p class="pricing-section__note-text">All plans include core expense tracking. Upgrade only when you need exports, admin tools, or team features.</p>
                </div>
                <div class="pricing-section__links">
                    <a href="{{ route('faq') }}" class="pricing-section__link">View FAQ</a>
                    <a href="{{ route('contact') }}" class="pricing-section__link">Talk to sales</a>
                </div>
            </div>
        @endif
    </div>
</section>
