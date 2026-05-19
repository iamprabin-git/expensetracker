<x-marketing-layout title="Features" metaDescription="Explore ExpenseTracker features: dashboards, transactions, categories, dark mode, and admin tools.">
    @include('pages.partials.page-hero', [
        'badge' => 'Features',
        'title' => 'Built for clarity, not clutter',
        'lead' => 'Every feature is designed to help you understand your money faster.',
    ])

    <section class="site-section">
        <div class="container">
            <div class="row g-4">
                @foreach ([
                    ['Dashboard overview', 'Real-time totals for income, expenses, net balance, and monthly snapshots.'],
                    ['Transaction management', 'Add, edit, filter, and delete income or expense entries with full details.'],
                    ['Smart categories', 'Built-in and custom categories with colors and income/expense types.'],
                    ['Search and filters', 'Find transactions by title or filter by type instantly.'],
                    ['Admin panel', 'Filament-powered /admin area for user and data management.'],
                    ['Role-based access', 'Secure separation between user dashboard and admin tools.'],
                ] as $item)
                    <div class="col-lg-6">
                        <article class="site-feature-card">
                            <h3 class="h5 fw-semibold mb-2">{{ $item[0] }}</h3>
                            <p class="text-secondary mb-0">{{ $item[1] }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-marketing-layout>
