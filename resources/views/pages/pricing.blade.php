<x-marketing-layout title="Pricing" metaDescription="Simple, transparent pricing for ExpenseTracker. Start free and upgrade when you need more.">
    @include('pages.partials.page-hero', [
        'badge' => 'Pricing',
        'title' => 'Simple plans for every budget',
        'lead' => 'Start free. Upgrade when you need advanced admin controls or team features.',
    ])

    <section class="site-section">
        <div class="container">
            <div class="row g-4 justify-content-center align-items-stretch">
                <div class="col-md-6 col-lg-4">
                    <div class="site-pricing-card">
                        <h3 class="h5 fw-bold">Free</h3>
                        <p class="display-6 fw-bold my-3">$0<span class="fs-6 fw-normal text-secondary">/mo</span></p>
                        <ul class="list-unstyled small text-secondary flex-grow-1 mb-4">
                            <li class="mb-2">✓ Unlimited personal transactions</li>
                            <li class="mb-2">✓ Custom categories</li>
                            <li class="mb-2">✓ Dashboard & reports</li>
                            <li class="mb-2">✓ Light / dark mode</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Get started</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="site-pricing-card site-pricing-card--featured">
                        <span class="badge bg-primary position-absolute top-0 end-0 m-3">Popular</span>
                        <h3 class="h5 fw-bold">Pro</h3>
                        <p class="display-6 fw-bold my-3">$9<span class="fs-6 fw-normal text-secondary">/mo</span></p>
                        <ul class="list-unstyled small text-secondary flex-grow-1 mb-4">
                            <li class="mb-2">✓ Everything in Free</li>
                            <li class="mb-2">✓ Export to CSV</li>
                            <li class="mb-2">✓ Priority email support</li>
                            <li class="mb-2">✓ Advanced filters</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-primary site-btn-primary w-100">Start Pro trial</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="site-pricing-card">
                        <h3 class="h5 fw-bold">Business</h3>
                        <p class="display-6 fw-bold my-3">$29<span class="fs-6 fw-normal text-secondary">/mo</span></p>
                        <ul class="list-unstyled small text-secondary flex-grow-1 mb-4">
                            <li class="mb-2">✓ Everything in Pro</li>
                            <li class="mb-2">✓ Filament admin access</li>
                            <li class="mb-2">✓ Multi-user management</li>
                            <li class="mb-2">✓ Dedicated support</li>
                        </ul>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">Contact sales</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
