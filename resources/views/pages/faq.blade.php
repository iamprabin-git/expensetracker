<x-marketing-layout title="FAQ" metaDescription="Frequently asked questions about ExpenseTracker accounts, features, and security.">
    @include('pages.partials.page-hero', [
        'badge' => 'FAQ',
        'title' => 'Frequently asked questions',
        'lead' => 'Quick answers to common questions about getting started and using ExpenseTracker.',
    ])

    <section class="site-section pt-0">
        <div class="container" style="max-width: 48rem;">
            <div class="accordion" id="faqAccordion">
                @foreach ([
                    ['Is ExpenseTracker free?', 'Yes. The Free plan includes unlimited personal transactions, categories, and dashboard access. Pro and Business plans add optional upgrades.'],
                    ['How do I access the admin panel?', 'Admin users log in with an admin account and are redirected to /admin, powered by Filament. Regular users use the standard dashboard.'],
                    ['Can I use it on mobile?', 'Yes. The entire user interface is responsive and works on phones, tablets, and desktops.'],
                    ['Is my data secure?', 'Each user only sees their own transactions. Admins manage the system through a separate panel with role-based access controls.'],
                    ['Can I export my data?', 'CSV export is included on Pro plans. Free users can view and manage all data within the dashboard.'],
                    ['How do I delete my account?', 'Go to Profile in your dashboard and use the delete account section at the bottom of the page.'],
                ] as $i => $faq)
                    <div class="site-faq-item mb-2 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                {{ $faq[0] }}
                            </button>
                        </h2>
                        <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">{{ $faq[1] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-marketing-layout>
