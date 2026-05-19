<x-marketing-layout title="About" metaDescription="Learn about ExpenseTracker and our mission to make personal finance tracking accessible.">
    @include('pages.partials.page-hero', [
        'badge' => 'About us',
        'title' => 'We believe finance tools should feel human',
        'lead' => 'ExpenseTracker was built to give everyone a clear picture of their money — without jargon or overwhelm.',
    ])

    <section class="site-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="site-section-title h3">Our story</h2>
                    <p class="text-secondary">We started ExpenseTracker after seeing friends struggle with bloated budgeting apps. We wanted something fast, honest, and beautiful — a tool you would actually open every week.</p>
                    <p class="text-secondary mb-0">Today we serve individuals and small teams who need income and expense tracking with optional admin oversight through Filament.</p>
                </div>
                <div class="col-lg-6">
                    <div class="card-panel">
                        <h3 class="h5 fw-semibold mb-3">Our values</h3>
                        <ul class="list-unstyled mb-0 d-grid gap-3">
                            <li><strong class="text-indigo-600 dark:text-indigo-400">Clarity</strong> — Numbers should be easy to read and act on.</li>
                            <li><strong class="text-indigo-600 dark:text-indigo-400">Privacy</strong> — Your financial data belongs to you.</li>
                            <li><strong class="text-indigo-600 dark:text-indigo-400">Accessibility</strong> — Works on any screen, in light or dark mode.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
