<x-marketing-layout metaDescription="Track income and expenses with ExpenseTracker. Simple dashboards, categories, and reports for personal finance.">
    {{-- Hero --}}
    <section class="site-hero site-section pb-0">
        <div class="site-hero__grid" aria-hidden="true"></div>
        <div class="container position-relative py-5 py-lg-6">
            <div class="row align-items-center g-5 py-4 py-lg-5">
                <div class="col-12 col-lg-6">
                    <span class="badge bg-white/15 text-white border border-white/25 mb-3 px-3 py-2">Personal finance, simplified</span>
                    <h1 class="display-4 fw-bold mb-4 lh-sm">Know exactly where your money goes</h1>
                    <p class="lead text-indigo-100 mb-4 pe-lg-4">
                        ExpenseTracker helps you record income and expenses, organize spending by category, and see your balance in real time — on any device.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold px-4">Start free today</a>
                        <a href="{{ route('features') }}" class="btn btn-outline-light btn-lg px-4">Explore features</a>
                    </div>
                    <p class="small text-indigo-200 mt-3 mb-0">No credit card required · Free plan available</p>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="site-hero__preview rounded-4 border border-white/20 bg-white/10 p-4 p-md-5 backdrop-blur shadow-lg">
                        <p class="small text-indigo-200 mb-3">Your data stays private</p>
                        <ul class="list-unstyled mb-0 d-grid gap-2">
                            <li class="d-flex gap-3 align-items-start rounded-3 bg-white/10 p-3"><span class="badge bg-success rounded-pill">✓</span><span class="small">Personal finances never appear on the public site</span></li>
                            <li class="d-flex gap-3 align-items-start rounded-3 bg-white/10 p-3"><span class="badge bg-success rounded-pill">✓</span><span class="small">Accounts require admin approval & membership</span></li>
                            <li class="d-flex gap-3 align-items-start rounded-3 bg-white/10 p-3"><span class="badge bg-success rounded-pill">✓</span><span class="small">Only approved reviews are published</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="site-section site-section--muted py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3"><div class="site-stat"><div class="site-stat__value">100%</div><div class="site-stat__label">Private by default</div></div></div>
                <div class="col-6 col-md-3"><div class="site-stat"><div class="site-stat__value">99.9%</div><div class="site-stat__label">Uptime target</div></div></div>
                <div class="col-6 col-md-3"><div class="site-stat"><div class="site-stat__value">2 min</div><div class="site-stat__label">Average setup</div></div></div>
                <div class="col-6 col-md-3"><div class="site-stat"><div class="site-stat__value">24/7</div><div class="site-stat__label">Access anywhere</div></div></div>
            </div>
        </div>
    </section>

    {{-- Features preview --}}
    <section class="site-section">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
                <h2 class="site-section-title">Everything you need to stay on budget</h2>
                <p class="site-section-lead">Powerful tools without the complexity. Built for everyday users, not accountants.</p>
            </div>
            <div class="row g-4">
                @foreach ([
                    ['title' => 'Smart dashboard', 'text' => 'See income, expenses, and net balance at a glance with monthly breakdowns.', 'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z'],
                    ['title' => 'Income & expenses', 'text' => 'Log transactions in seconds with categories, notes, and custom dates.', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['title' => 'Custom categories', 'text' => 'Color-coded categories for groceries, bills, salary, and more.', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z'],
                    ['title' => 'Light & dark mode', 'text' => 'Comfortable viewing day or night with a single click in the header.', 'icon' => 'M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z'],
                    ['title' => 'Mobile responsive', 'text' => 'Full experience on phone, tablet, and desktop with Bootstrap + Tailwind.', 'icon' => 'M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3'],
                    ['title' => 'Secure by design', 'text' => 'Your data is scoped to your account with role-based admin access.', 'icon' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
                ] as $feature)
                    <div class="col-md-6 col-lg-4">
                        <article class="site-feature-card">
                            <span class="site-feature-card__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" /></svg>
                            </span>
                            <h3 class="h5 fw-semibold mb-2">{{ $feature['title'] }}</h3>
                            <p class="text-secondary mb-0 small">{{ $feature['text'] }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('features') }}" class="btn btn-outline-primary btn-lg">View all features</a>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="site-section site-section--muted">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="site-section-title">Up and running in three steps</h2>
            </div>
            <div class="row g-4">
                @foreach ([['1', 'Create your account', 'Sign up in under a minute with email and password.'], ['2', 'Add transactions', 'Record income and expenses or use demo categories.'], ['3', 'Track progress', 'Watch your dashboard update with balances and trends.']] as $step)
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-indigo-600 text-white d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width:3rem;height:3rem;">{{ $step[0] }}</div>
                        <h3 class="h5 fw-semibold">{{ $step[1] }}</h3>
                        <p class="text-secondary small mb-0">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('pages.partials.reviews-section', ['reviews' => $reviews])

    {{-- CTA --}}
    <section class="site-section">
        <div class="container">
            <div class="site-cta">
                <h2 class="display-6 fw-bold mb-3">Ready to take control of your finances?</h2>
                <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 32rem;">Join ExpenseTracker today and start building better money habits.</p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold px-5">Create free account</a>
            </div>
        </div>
    </section>
</x-marketing-layout>
