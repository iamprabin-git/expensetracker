<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

class SitePageSeeder extends Seeder
{
    public function run(): void
    {
        $app = config('app.name', 'Mero Expense Tracker');

        foreach ($this->pages($app) as $page) {
            SitePage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                $page,
            );
        }
    }

    /** @return list<array<string, mixed>> */
    private function pages(string $app): array
    {
        return [
            $this->homePage($app),
            $this->featuresPage($app),
            $this->pricingPage($app),
            $this->aboutPage($app),
            $this->faqPage($app),
            $this->contactPage($app),
            $this->privacyPage($app),
            $this->termsPage($app),
        ];
    }

    private function homePage(string $app): array
    {
        return [
            'slug' => 'home',
            'label' => 'Home',
            'title' => 'Home',
            'meta_description' => "Track income and expenses with {$app}. Simple dashboards, categories, and reports for personal finance.",
            'is_published' => true,
            'hero_badge' => 'Personal finance, simplified',
            'hero_title' => 'Know exactly where your money goes',
            'hero_lead' => "{$app} helps you record income and expenses, organize spending by category, and see your balance in real time — on any device.",
            'hero_image' => null,
            'body_html' => null,
            'extras' => [
                'hero_note' => 'No credit card required · Free plan available',
                'primary_cta_label' => 'Start free today',
                'primary_cta_url' => '/register',
                'secondary_cta_label' => 'Explore features',
                'secondary_cta_url' => '/features',
            ],
            'sections' => [
                [
                    'type' => 'hero_bullets',
                    'title' => 'Your data stays private',
                    'items' => [
                        ['text' => 'Personal finances never appear on the public site'],
                        ['text' => 'Accounts require admin approval & membership'],
                        ['text' => 'Only approved reviews are published'],
                    ],
                ],
                [
                    'type' => 'stats',
                    'items' => [
                        ['value' => '100%', 'label' => 'Private by default'],
                        ['value' => '99.9%', 'label' => 'Uptime target'],
                        ['value' => '2 min', 'label' => 'Average setup'],
                        ['value' => '24/7', 'label' => 'Access anywhere'],
                    ],
                ],
                [
                    'type' => 'section_header',
                    'title' => 'Everything you need to stay on budget',
                    'subtitle' => 'Powerful tools without the complexity. Built for everyday users, not accountants.',
                ],
                [
                    'type' => 'feature_cards',
                    'items' => [
                        ['title' => 'Smart dashboard', 'text' => 'See income, expenses, and net balance at a glance with monthly breakdowns.', 'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z'],
                        ['title' => 'Income & expenses', 'text' => 'Log transactions in seconds with categories, notes, and custom dates.', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                        ['title' => 'Custom categories', 'text' => 'Color-coded categories for groceries, bills, salary, and more.', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z'],
                        ['title' => 'Light & dark mode', 'text' => 'Comfortable viewing day or night with a single click in the header.', 'icon' => 'M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z'],
                        ['title' => 'Mobile responsive', 'text' => 'Full experience on phone, tablet, and desktop with Bootstrap + Tailwind.', 'icon' => 'M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3'],
                        ['title' => 'Secure by design', 'text' => 'Your data is scoped to your account with role-based admin access.', 'icon' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
                    ],
                ],
                [
                    'type' => 'section_header',
                    'title' => 'Up and running in three steps',
                    'subtitle' => null,
                ],
                [
                    'type' => 'steps',
                    'items' => [
                        ['title' => 'Create your account', 'text' => 'Sign up in under a minute with email and password.'],
                        ['title' => 'Add transactions', 'text' => 'Record income and expenses or use demo categories.'],
                        ['title' => 'Track progress', 'text' => 'Watch your dashboard update with balances and trends.'],
                    ],
                ],
                [
                    'type' => 'reviews',
                    'title' => 'What members say',
                    'subtitle' => 'Reviews are submitted on this page and published only after admin approval. We never show private account or financial data.',
                ],
                [
                    'type' => 'cta',
                    'title' => 'Ready to take control of your finances?',
                    'subtitle' => "Join {$app} today and start building better money habits.",
                    'items' => [
                        ['link_label' => 'Create free account', 'link_url' => '/register'],
                    ],
                ],
            ],
        ];
    }

    private function featuresPage(string $app): array
    {
        return [
            'slug' => 'features',
            'label' => 'Features',
            'title' => 'Features',
            'meta_description' => "Explore {$app} features: dashboards, transactions, categories, dark mode, and admin tools.",
            'is_published' => true,
            'hero_badge' => 'Features',
            'hero_title' => 'Built for clarity, not clutter',
            'hero_lead' => 'Every feature is designed to help you understand your money faster.',
            'hero_image' => null,
            'body_html' => null,
            'extras' => [],
            'sections' => [
                [
                    'type' => 'feature_cards',
                    'items' => [
                        ['title' => 'Dashboard overview', 'text' => 'Real-time totals for income, expenses, net balance, and monthly snapshots.'],
                        ['title' => 'Transaction management', 'text' => 'Add, edit, filter, and delete income or expense entries with full details.'],
                        ['title' => 'Smart categories', 'text' => 'Built-in and custom categories with colors and income/expense types.'],
                        ['title' => 'Search and filters', 'text' => 'Find transactions by title or filter by type instantly.'],
                        ['title' => 'Admin panel', 'text' => 'Filament-powered /admin area for user and data management.'],
                        ['title' => 'Role-based access', 'text' => 'Secure separation between user dashboard and admin tools.'],
                    ],
                ],
            ],
        ];
    }

    private function pricingPage(string $app): array
    {
        return [
            'slug' => 'pricing',
            'label' => 'Pricing',
            'title' => 'Pricing',
            'meta_description' => "Simple, transparent pricing for {$app}. Start free and upgrade when you need more.",
            'is_published' => true,
            'hero_badge' => 'Pricing',
            'hero_title' => 'Simple plans for every budget',
            'hero_lead' => 'Start free. Upgrade when you need advanced admin controls or team features.',
            'hero_image' => null,
            'body_html' => null,
            'extras' => [],
            'sections' => [
                [
                    'type' => 'pricing',
                    'items' => [
                        [
                            'title' => 'Free',
                            'price' => '$0',
                            'period' => '/mo',
                            'features' => "Unlimited personal transactions\nCustom categories\nDashboard & reports\nLight / dark mode",
                            'link_label' => 'Get started',
                            'link_url' => '/register',
                        ],
                        [
                            'title' => 'Pro',
                            'price' => '$9',
                            'period' => '/mo',
                            'badge' => 'Popular',
                            'featured' => true,
                            'features' => "Everything in Free\nExport to CSV\nPriority email support\nAdvanced filters",
                            'link_label' => 'Start Pro trial',
                            'link_url' => '/register',
                        ],
                        [
                            'title' => 'Business',
                            'price' => '$29',
                            'period' => '/mo',
                            'features' => "Everything in Pro\nFilament admin access\nMulti-user management\nDedicated support",
                            'link_label' => 'Contact sales',
                            'link_url' => '/contact',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function aboutPage(string $app): array
    {
        return [
            'slug' => 'about',
            'label' => 'About',
            'title' => 'About',
            'meta_description' => "Learn about {$app} and our mission to make personal finance tracking accessible.",
            'is_published' => true,
            'hero_badge' => 'About us',
            'hero_title' => 'We believe finance tools should feel human',
            'hero_lead' => "{$app} was built to give everyone a clear picture of their money — without jargon or overwhelm.",
            'hero_image' => null,
            'body_html' => null,
            'extras' => [],
            'sections' => [
                [
                    'type' => 'image_text',
                    'title' => 'Our story',
                    'items' => [
                        ['text' => "We started {$app} after seeing friends struggle with bloated budgeting apps. We wanted something fast, honest, and beautiful — a tool you would actually open every week."],
                        ['text' => 'Today we serve individuals and small teams who need income and expense tracking with optional admin oversight through Filament.'],
                    ],
                ],
                [
                    'type' => 'values',
                    'title' => 'Our values',
                    'items' => [
                        ['title' => 'Clarity', 'text' => 'Numbers should be easy to read and act on.'],
                        ['title' => 'Privacy', 'text' => 'Your financial data belongs to you.'],
                        ['title' => 'Accessibility', 'text' => 'Works on any screen, in light or dark mode.'],
                    ],
                ],
            ],
        ];
    }

    private function faqPage(string $app): array
    {
        return [
            'slug' => 'faq',
            'label' => 'FAQ',
            'title' => 'FAQ',
            'meta_description' => "Frequently asked questions about {$app} accounts, features, and security.",
            'is_published' => true,
            'hero_badge' => 'FAQ',
            'hero_title' => 'Frequently asked questions',
            'hero_lead' => "Quick answers to common questions about getting started and using {$app}.",
            'hero_image' => null,
            'body_html' => null,
            'extras' => [],
            'sections' => [
                [
                    'type' => 'faq',
                    'items' => [
                        ['title' => "Is {$app} free?", 'text' => 'Yes. The Free plan includes unlimited personal transactions, categories, and dashboard access. Pro and Business plans add optional upgrades.'],
                        ['title' => 'How do I access the admin panel?', 'text' => 'Admin users log in with an admin account and are redirected to /admin, powered by Filament. Regular users use the standard dashboard.'],
                        ['title' => 'Can I use it on mobile?', 'text' => 'Yes. The entire user interface is responsive and works on phones, tablets, and desktops.'],
                        ['title' => 'Is my data secure?', 'text' => 'Each user only sees their own transactions. Admins manage the system through a separate panel with role-based access controls.'],
                        ['title' => 'Can I export my data?', 'text' => 'CSV export is included on Pro plans. Free users can view and manage all data within the dashboard.'],
                        ['title' => 'How do I delete my account?', 'text' => 'Go to Profile in your dashboard and use the delete account section at the bottom of the page.'],
                    ],
                ],
            ],
        ];
    }

    private function contactPage(string $app): array
    {
        return [
            'slug' => 'contact',
            'label' => 'Contact',
            'title' => 'Contact',
            'meta_description' => "Contact the {$app} team for support, sales, or partnership inquiries.",
            'is_published' => true,
            'hero_badge' => 'Contact',
            'hero_title' => 'We would love to hear from you',
            'hero_lead' => 'Questions about your account, pricing, or partnerships? Send us a message.',
            'hero_image' => null,
            'body_html' => null,
            'extras' => [
                'sidebar_title' => 'Get in touch',
                'form_title' => 'Send a message',
                'success_message' => 'Message received! Our team will review it and reply to your email soon.',
            ],
            'sections' => [
                [
                    'type' => 'contact_info',
                    'items' => [
                        ['title' => 'Email', 'text' => 'info.meroexpensetracker@gmail.com'],
                        ['title' => 'Hours', 'text' => 'Mon–Fri, 9am–6pm (UTC)'],
                        ['title' => 'Response time', 'text' => 'Within 2 business days'],
                    ],
                ],
            ],
        ];
    }

    private function privacyPage(string $app): array
    {
        return [
            'slug' => 'privacy',
            'label' => 'Privacy Policy',
            'title' => 'Privacy Policy',
            'meta_description' => "{$app} privacy policy — how we collect, use, and protect your data.",
            'is_published' => true,
            'hero_badge' => null,
            'hero_title' => 'Privacy Policy',
            'hero_lead' => 'Last updated: '.now()->format('F j, Y'),
            'hero_image' => null,
            'sections' => [],
            'body_html' => <<<HTML
<p>{$app} ("we", "our") respects your privacy. This policy explains what information we collect and how we use it when you use our website and application.</p>
<h2>Information we collect</h2>
<p>We collect information you provide directly, such as your name, email address, and financial transaction data you enter into the application.</p>
<h2>How we use information</h2>
<p>We use your information to provide the service, maintain your account, improve our product, and respond to support requests. We do not sell your personal data to third parties.</p>
<h2>Data security</h2>
<p>We implement industry-standard measures including encrypted passwords, access controls, and role-based permissions to protect your data.</p>
<h2>Your rights</h2>
<p>You may update your profile information at any time or delete your account from the profile settings page.</p>
<h2>Contact</h2>
<p>For privacy-related questions, contact us at <a href="/contact">our contact page</a>.</p>
HTML,
            'extras' => [],
        ];
    }

    private function termsPage(string $app): array
    {
        return [
            'slug' => 'terms',
            'label' => 'Terms of Service',
            'title' => 'Terms of Service',
            'meta_description' => "{$app} terms of service and conditions of use.",
            'is_published' => true,
            'hero_badge' => null,
            'hero_title' => 'Terms of Service',
            'hero_lead' => 'Last updated: '.now()->format('F j, Y'),
            'hero_image' => null,
            'sections' => [],
            'body_html' => <<<HTML
<p>By using {$app}, you agree to these terms. Please read them carefully.</p>
<h2>Acceptable use</h2>
<p>You agree to use the service only for lawful purposes and not to misuse, disrupt, or attempt unauthorized access to the platform or other users' data.</p>
<h2>Accounts</h2>
<p>You are responsible for maintaining the confidentiality of your login credentials and for all activity under your account.</p>
<h2>Service availability</h2>
<p>We strive for high availability but do not guarantee uninterrupted access. We may modify or discontinue features with reasonable notice where possible.</p>
<h2>Limitation of liability</h2>
<p>{$app} is provided "as is" for personal finance tracking. We are not a financial advisor and are not liable for decisions made based on data in the app.</p>
<h2>Changes</h2>
<p>We may update these terms from time to time. Continued use after changes constitutes acceptance of the updated terms.</p>
HTML,
            'extras' => [],
        ];
    }
}
