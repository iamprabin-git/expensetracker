<section class="site-section site-section--muted" id="reviews">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
            <h2 class="site-section-title">What members say</h2>
            <p class="site-section-lead">Reviews are submitted on this page and published only after admin approval. We never show private account or financial data.</p>
        </div>

        @if (session('success') || session('info'))
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    @if (session('success'))
                        <div class="alert alert-success mb-0">{{ session('success') }}</div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info mb-0">{{ session('info') }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if ($reviews->isNotEmpty())
            <div class="row g-4 mb-5">
                @foreach ($reviews as $review)
                    <div class="col-md-6 col-lg-4">
                        <article class="site-feature-card h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <strong class="text-slate-900 dark:text-white">{{ $review->display_name }}</strong>
                                <span class="text-warning" aria-label="{{ $review->rating }} out of 5 stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </span>
                            </div>
                            <p class="text-secondary mb-2 small">{{ \Illuminate\Support\Str::limit($review->content, 180) }}</p>
                            <p class="text-secondary mb-0" style="font-size:0.75rem;">{{ $review->approved_at?->format('M Y') }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-secondary mb-5">Approved reviews will appear here. Submit yours below.</p>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-panel">
                    <h3 class="h5 fw-semibold mb-3">Submit a review</h3>
                    <p class="small text-secondary mb-3">Your review is sent from the website and shown publicly only after an admin approves it. Only your display name and review text are published — never your email or transactions.</p>
                    <form method="POST" action="{{ route('reviews.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="label-app" for="display_name">Public display name</label>
                            <input type="text" name="display_name" id="display_name" class="input-app form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name', auth()->user()?->name) }}" maxlength="80" required>
                            @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="label-app" for="rating">Rating</label>
                            <select name="rating" id="rating" class="input-app form-select @error('rating') is-invalid @enderror" required>
                                @for ($r = 5; $r >= 1; $r--)
                                    <option value="{{ $r }}" @selected(old('rating', 5) == $r)>{{ $r }} stars</option>
                                @endfor
                            </select>
                            @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="label-app" for="content">Your review</label>
                            <textarea name="content" id="content" rows="4" class="input-app form-control @error('content') is-invalid @enderror" required minlength="20" maxlength="2000" placeholder="Tell others about your experience…">{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn site-btn-primary">Submit for approval</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
