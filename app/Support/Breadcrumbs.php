<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Reminder;
use App\Models\Transaction;
use App\Services\SiteContentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Breadcrumbs
{
    /** @var array<string, string> */
    private const MARKETING_ROUTES = [
        'home' => 'Home',
        'features' => 'Features',
        'pricing' => 'Pricing',
        'about' => 'About',
        'faq' => 'FAQ',
        'contact' => 'Contact',
        'privacy' => 'Privacy',
        'terms' => 'Terms',
        'login' => 'Log in',
        'register' => 'Register',
        'password.request' => 'Forgot password',
        'password.reset' => 'Reset password',
        'verification.notice' => 'Verify email',
        'password.confirm' => 'Confirm password',
        'account.pending' => 'Account pending',
        'account.expired' => 'Membership expired',
    ];

    /** @var array<string, string> */
    private const REPORT_TITLES = [
        'trial-balance' => 'Trial Balance',
        'profit-loss' => 'Profit & Loss',
        'balance-sheet' => 'Balance Sheet',
        'cash-flow' => 'Cash Flow Statement',
        'transaction-statement' => 'Transaction Statement',
    ];

    /** @var array<string, string> */
    private const APP_ROUTES = [
        'dashboard' => 'Dashboard',
        'analysis.index' => 'Analysis',
        'reports.index' => 'Reports',
        'transactions.index' => 'Transactions',
        'transactions.create' => 'New transaction',
        'transactions.edit' => 'Edit transaction',
        'categories.index' => 'Categories',
        'categories.create' => 'New category',
        'categories.edit' => 'Edit category',
        'budgets.index' => 'Budget planning',
        'reminders.index' => 'Reminders',
        'reminders.create' => 'New reminder',
        'reminders.edit' => 'Edit reminder',
        'ai-scan.index' => 'AI Scan',
        'settings.index' => 'Settings',
        'notifications.index' => 'Notifications',
    ];

    /**
     * @param  list<array{label: string, url?: string|null}>|null  $override
     * @return list<array{label: string, url: string|null}>
     */
    public static function resolve(?array $override = null): array
    {
        if ($override !== null) {
            return self::normalize($override);
        }

        $route = Route::currentRouteName();

        if ($route === null) {
            return [self::home()];
        }

        if ($route === 'home') {
            return [self::current('Home')];
        }

        if ($route === 'pages.show') {
            return self::marketingTrail(self::cmsPageLabel());
        }

        if (isset(self::MARKETING_ROUTES[$route])) {
            return self::marketingTrail(self::MARKETING_ROUTES[$route]);
        }

        if ($route === 'reports.show' || $route === 'reports.pdf') {
            return self::appTrail(
                ['label' => 'Reports', 'url' => route('reports.index')],
                self::current(self::reportTitle()),
            );
        }

        if (isset(self::APP_ROUTES[$route])) {
            $label = self::APP_ROUTES[$route];

            if ($route === 'categories.edit' && ($category = request()->route('category')) instanceof Category) {
                $label = $category->name;
            }

            if ($route === 'transactions.edit' && ($transaction = request()->route('transaction')) instanceof Transaction) {
                $label = $transaction->title ?: 'Edit transaction';
            }

            if ($route === 'reminders.edit' && ($reminder = request()->route('reminder')) instanceof Reminder) {
                $label = $reminder->title ?: 'Edit reminder';
            }

            return self::appTrail(self::current($label));
        }

        return self::marketingTrail(Str::headline(str_replace('.', ' ', $route)));
    }

    /**
     * @param  list<array{label: string, url?: string|null}>  $items
     * @return list<array{label: string, url: string|null}>
     */
    private static function normalize(array $items): array
    {
        $normalized = [];

        foreach ($items as $index => $item) {
            $isLast = $index === array_key_last($items);
            $normalized[] = [
                'label' => $item['label'],
                'url' => $isLast ? null : ($item['url'] ?? null),
            ];
        }

        return $normalized;
    }

    /** @return array{label: string, url: string|null} */
    private static function home(): array
    {
        return ['label' => 'Home', 'url' => route('home')];
    }

    /** @return array{label: string, url: string|null} */
    private static function current(string $label): array
    {
        return ['label' => $label, 'url' => null];
    }

    /** @return array{label: string, url: string} */
    private static function link(string $label, string $url): array
    {
        return ['label' => $label, 'url' => $url];
    }

    /**
     * @param  array{label: string, url?: string|null}|string  ...$segments
     * @return list<array{label: string, url: string|null}>
     */
    private static function marketingTrail(array|string ...$segments): array
    {
        $trail = [self::home()];

        foreach ($segments as $segment) {
            $trail[] = is_string($segment)
                ? self::current($segment)
                : [
                    'label' => $segment['label'],
                    'url' => $segment['url'] ?? null,
                ];
        }

        if ($trail !== []) {
            $trail[array_key_last($trail)]['url'] = null;
        }

        return $trail;
    }

    /**
     * @param  array{label: string, url?: string|null}|string  ...$segments
     * @return list<array{label: string, url: string|null}>
     */
    private static function appTrail(array|string ...$segments): array
    {
        $trail = [
            self::home(),
            self::link('Dashboard', route('dashboard')),
        ];

        foreach ($segments as $segment) {
            $trail[] = is_string($segment)
                ? self::current($segment)
                : [
                    'label' => $segment['label'],
                    'url' => $segment['url'] ?? null,
                ];
        }

        if ($trail !== []) {
            $trail[array_key_last($trail)]['url'] = null;
        }

        return $trail;
    }

    private static function cmsPageLabel(): string
    {
        $slug = (string) request()->route('slug', '');

        try {
            $page = app(SiteContentService::class)->get($slug);

            return $page->label ?: $page->title ?: Str::headline(str_replace('-', ' ', $slug));
        } catch (\Throwable) {
            return Str::headline(str_replace('-', ' ', $slug));
        }
    }

    private static function reportTitle(): string
    {
        $key = (string) request()->route('report', '');

        return self::REPORT_TITLES[$key] ?? Str::headline(str_replace('-', ' ', $key));
    }
}
