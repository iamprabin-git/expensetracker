@push('styles')
    @vite(['resources/css/reviews-carousel.css'])
@endpush

@push('scripts')
    @vite(['resources/js/reviews-carousel.js'])
@endpush

<section class="site-section site-section--muted" id="reviews">
    <div class="mx-auto w-full max-w-6xl px-4">
        <div class="mx-auto mb-8 max-w-3xl text-center">
            <h2 class="site-section-title">{{ $sectionTitle ?? 'What members say' }}</h2>
            <p class="site-section-lead">{{ $sectionSubtitle ?? 'Reviews are submitted on this page and published only after admin approval. We never show private account or financial data.' }}</p>
        </div>

        <div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-2 lg:gap-8">
            {{-- Left: reviews carousel --}}
            <div class="reviews-split__carousel">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h3 class="text-lg font-semibold text-foreground">Member reviews</h3>
                    @if ($reviews->isNotEmpty())
                        <span class="text-xs text-muted-foreground">{{ $reviews->count() }} approved</span>
                    @endif
                </div>

                @if ($reviews->isNotEmpty())
                    <div class="swiper reviews-swiper flex-1" data-reviews-swiper>
                        <div class="swiper-wrapper">
                            @foreach ($reviews as $review)
                                <div class="swiper-slide">
                                    <article class="reviews-swiper-card">
                                        <blockquote class="reviews-swiper-card__quote">
                                            {{ $review->content }}
                                        </blockquote>
                                        <footer class="reviews-swiper-card__footer flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                                            @include('pages.partials.review-author', ['review' => $review])
                                            @include('pages.partials.review-stars', ['rating' => $review->rating, 'size' => 'lg', 'class' => 'shrink-0 self-start sm:self-center'])
                                        </footer>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        <div class="reviews-swiper-controls">
                            <div class="reviews-swiper-nav">
                                <button type="button" class="reviews-swiper-prev" aria-label="Previous review">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.56 9.5H16a.75.75 0 0 1 0 1.5H8.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.25a.75.75 0 0 1 0-1.06l4.5-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button type="button" class="reviews-swiper-next" aria-label="Next review">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.44 11H4a.75.75 0 0 1 0-1.5h7.44L8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="reviews-swiper-pagination"></div>
                        </div>
                    </div>
                @else
                    <div class="reviews-swiper-empty flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mb-3 size-10 text-primary/40" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <p class="mb-1 text-sm font-medium text-foreground">No reviews yet</p>
                        <p class="text-sm">Be the first to share your experience using the form.</p>
                    </div>
                @endif
            </div>

            {{-- Right: submit form --}}
            <div class="flex flex-col">
                <x-ui.card
                    class="h-full"
                    title="Submit a review"
                    description="Published only after admin approval. We never show your email or financial data."
                >
                    @auth
                        <div class="mb-5 flex items-center gap-3 rounded-lg border border-border bg-muted/40 p-3">
                            <x-user-avatar :user="auth()->user()" size="lg" />
                            <div class="min-w-0">
                                <p class="font-semibold text-foreground">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-muted-foreground">
                                    @if (auth()->user()->google_id)
                                        Signed in with Google
                                        <span class="mx-1 opacity-50" aria-hidden="true">·</span>
                                    @endif
                                    {{ now()->format('F j, Y') }}
                                </p>
                            </div>
                        </div>
                    @endauth

                    <form
                        method="POST"
                        action="{{ route('reviews.store') }}"
                        class="space-y-5"
                        x-data="{
                            rating: {{ (int) old('rating', 5) }},
                            contentLength: {{ strlen(old('content', '')) }}
                        }"
                    >
                        @csrf

                        <div class="space-y-2">
                            <x-ui.label for="display_name">Public display name</x-ui.label>
                            <x-ui.input
                                type="text"
                                name="display_name"
                                id="display_name"
                                value="{{ old('display_name', auth()->user()?->name) }}"
                                maxlength="80"
                                required
                                placeholder="e.g. Alex M."
                                @class(['border-destructive ring-destructive/20' => $errors->has('display_name')])
                            />
                            <x-ui.field-error :messages="$errors->get('display_name')" />
                        </div>

                        <div class="space-y-2">
                            <x-ui.label id="rating-label">Rating</x-ui.label>
                            <input type="hidden" name="rating" :value="rating">
                            <div
                                class="flex h-9 items-center gap-1 rounded-md border border-input bg-background px-3 dark:bg-input/30"
                                role="radiogroup"
                                aria-labelledby="rating-label"
                            >
                                @for ($star = 1; $star <= 5; $star++)
                                    <button
                                        type="button"
                                        class="rounded p-0.5 text-amber-500 transition hover:scale-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring dark:text-amber-400"
                                        :class="rating >= {{ $star }} ? 'opacity-100' : 'opacity-30'"
                                        @click="rating = {{ $star }}"
                                        :aria-checked="rating === {{ $star }}"
                                        aria-label="{{ $star }} {{ $star === 1 ? 'star' : 'stars' }}"
                                        role="radio"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.052 2.52c-.192.46-.597.788-1.065.98l-2.52 1.052a.92.92 0 00-.53 1.567l1.919 1.566c.365.298.526.78.433 1.24l-.6 2.47a.92.92 0 001.33 1.003l2.194-1.337a.92.92 0 011.124 0l2.194 1.337a.92.92 0 001.33-1.003l-.6-2.47a.92.92 0 00.433-1.24l1.919-1.566a.92.92 0 00-.53-1.567l-2.52-1.052a.92.92 0 00-1.065-.98l-1.052-2.52z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endfor
                                <span class="ml-2 text-sm text-muted-foreground" x-text="rating + ' / 5'"></span>
                            </div>
                            <x-ui.field-error :messages="$errors->get('rating')" />
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <x-ui.label for="content">Your review</x-ui.label>
                                <span
                                    class="text-xs text-muted-foreground"
                                    x-text="contentLength + ' / 2000'"
                                ></span>
                            </div>
                            <textarea
                                name="content"
                                id="content"
                                rows="5"
                                required
                                minlength="20"
                                maxlength="2000"
                                placeholder="Tell others about your experience…"
                                @input="contentLength = $event.target.value.length"
                                class="flex min-h-[6.5rem] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30 {{ $errors->has('content') ? 'border-destructive ring-destructive/20' : '' }}"
                            >{{ old('content') }}</textarea>
                            <p class="text-xs text-muted-foreground">Minimum 20 characters. Your photo may appear after approval if you signed in with Google.</p>
                            <x-ui.field-error :messages="$errors->get('content')" />
                        </div>

                        <div class="border-t border-border pt-4">
                            <x-ui.button type="submit" class="w-full">
                                Submit for approval
                            </x-ui.button>
                            <p class="mt-3 text-center text-xs text-muted-foreground">
                                Only your display name and review text may be shown publicly.
                            </p>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>
</section>
