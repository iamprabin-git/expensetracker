<x-marketing-layout title="Privacy Policy" metaDescription="ExpenseTracker privacy policy — how we collect, use, and protect your data.">
    @include('pages.partials.page-hero', ['title' => 'Privacy Policy', 'lead' => 'Last updated: '.now()->format('F j, Y')])

    <section class="site-section pt-0">
        <div class="container site-legal" style="max-width: 48rem;">
            <p>ExpenseTracker ("we", "our") respects your privacy. This policy explains what information we collect and how we use it when you use our website and application.</p>

            <h2>Information we collect</h2>
            <p>We collect information you provide directly, such as your name, email address, and financial transaction data you enter into the application.</p>

            <h2>How we use information</h2>
            <p>We use your information to provide the service, maintain your account, improve our product, and respond to support requests. We do not sell your personal data to third parties.</p>

            <h2>Data security</h2>
            <p>We implement industry-standard measures including encrypted passwords, access controls, and role-based permissions to protect your data.</p>

            <h2>Your rights</h2>
            <p>You may update your profile information at any time or delete your account from the profile settings page.</p>

            <h2>Contact</h2>
            <p>For privacy-related questions, contact us at <a href="{{ route('contact') }}">our contact page</a>.</p>
        </div>
    </section>
</x-marketing-layout>
