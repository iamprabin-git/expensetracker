@props([
    'items' => [],
    'title' => null,
    'subtitle' => null,
])

@php
    $faqItems = collect($items)
        ->filter(fn ($item) => filled($item['title'] ?? null) || filled($item['text'] ?? null))
        ->values()
        ->map(fn ($item) => [
            'title' => $item['title'] ?? '',
            'text' => $item['text'] ?? '',
        ])
        ->all();
@endphp

<section
    class="faq-section site-section"
    id="faq"
    x-data="{
        open: 0,
        query: '',
        items: @js($faqItems),
        filteredIndexes() {
            const q = this.query.trim().toLowerCase();
            if (!q) {
                return this.items.map((_, i) => i);
            }
            return this.items
                .map((item, i) => (
                    item.title.toLowerCase().includes(q) || item.text.toLowerCase().includes(q) ? i : -1
                ))
                .filter((i) => i >= 0);
        },
        isVisible(index) {
            return this.filteredIndexes().includes(index);
        },
        hasResults() {
            return this.filteredIndexes().length > 0;
        },
        toggle(index) {
            this.open = this.open === index ? -1 : index;
        }
    }"
>
    <div class="mx-auto w-full max-w-6xl px-4">
        <div class="faq-section__layout">
            <aside class="faq-section__aside">
                <div class="faq-section__intro">
                    <h2 class="faq-section__title">{{ $title ?? 'Common questions' }}</h2>
                    <p class="faq-section__subtitle">
                        {{ $subtitle ?? 'Browse answers below or search by keyword. Can’t find what you need? Our team is happy to help.' }}
                    </p>
                </div>

                <div class="faq-section__search">
                    <label class="faq-section__search-label" for="faq-search">Search questions</label>
                    <div class="faq-section__search-field">
                        <svg class="faq-section__search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input
                            id="faq-search"
                            type="search"
                            class="faq-section__search-input"
                            placeholder="e.g. pricing, security, mobile…"
                            autocomplete="off"
                            x-model="query"
                        >
                    </div>
                    <p class="faq-section__search-hint" x-show="!query.trim()">{{ count($faqItems) }} questions</p>
                    <p class="faq-section__search-hint" x-show="query.trim()" x-cloak>
                        <span x-text="filteredIndexes().length"></span> of {{ count($faqItems) }} match
                    </p>
                </div>

                <div class="faq-section__help-card">
                    <p class="faq-section__help-eyebrow">Need more help?</p>
                    <p class="faq-section__help-text">Reach out for account, billing, or technical support.</p>
                    <x-ui.button href="{{ route('contact') }}">Contact support</x-ui.button>
                </div>
            </aside>

            <div class="faq-section__panel">
                @if (count($faqItems) === 0)
                    <p class="faq-section__empty">No questions have been published yet.</p>
                @else
                    <div class="faq-section__list">
                        @foreach ($faqItems as $i => $item)
                            <div
                                class="faq-section__item"
                                x-show="isVisible({{ $i }})"
                                x-cloak
                            >
                                <h3 class="faq-section__question">
                                    <button
                                        type="button"
                                        class="faq-section__trigger"
                                        :class="{ 'is-open': open === {{ $i }} }"
                                        :aria-expanded="open === {{ $i }} ? 'true' : 'false'"
                                        aria-controls="faq-answer-{{ $i }}"
                                        id="faq-question-{{ $i }}"
                                        @click="toggle({{ $i }})"
                                    >
                                        <span class="faq-section__trigger-index" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="faq-section__trigger-text">{{ $item['title'] }}</span>
                                        <span class="faq-section__trigger-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </span>
                                    </button>
                                </h3>
                                <div
                                    id="faq-answer-{{ $i }}"
                                    class="faq-section__answer"
                                    role="region"
                                    aria-labelledby="faq-question-{{ $i }}"
                                    x-show="open === {{ $i }}"
                                    x-transition:enter="faq-section__answer-enter"
                                    x-transition:enter-start="faq-section__answer-enter-start"
                                    x-transition:enter-end="faq-section__answer-enter-end"
                                    x-transition:leave="faq-section__answer-leave"
                                    x-transition:leave-start="faq-section__answer-leave-start"
                                    x-transition:leave-end="faq-section__answer-leave-end"
                                    x-cloak
                                >
                                    <div class="faq-section__answer-inner">
                                        <p>{{ $item['text'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="faq-section__no-results" x-show="query.trim() && !hasResults()" x-cloak>
                        No questions match your search. Try different keywords or
                        <a href="{{ route('contact') }}" class="faq-section__link">contact us</a>.
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
