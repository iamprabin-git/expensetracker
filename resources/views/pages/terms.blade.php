<x-marketing-layout title="Terms of Service" metaDescription="ExpenseTracker terms of service and conditions of use.">
    @include('pages.partials.page-hero', ['title' => 'Terms of Service', 'lead' => 'Last updated: '.now()->format('F j, Y')])

    <section class="site-section pt-0">
        <div class="container site-legal" style="max-width: 48rem;">
            <p>By using ExpenseTracker, you agree to these terms. Please read them carefully.</p>

            <h2>Acceptable use</h2>
            <p>You agree to use the service only for lawful purposes and not to misuse, disrupt, or attempt unauthorized access to the platform or other users' data.</p>

            <h2>Accounts</h2>
            <p>You are responsible for maintaining the confidentiality of your login credentials and for all activity under your account.</p>

            <h2>Service availability</h2>
            <p>We strive for high availability but do not guarantee uninterrupted access. We may modify or discontinue features with reasonable notice where possible.</p>

            <h2>Limitation of liability</h2>
            <p>ExpenseTracker is provided "as is" for personal finance tracking. We are not a financial advisor and are not liable for decisions made based on data in the app.</p>

            <h2>Changes</h2>
            <p>We may update these terms from time to time. Continued use after changes constitutes acceptance of the updated terms.</p>
        </div>
    </section>
</x-marketing-layout>
