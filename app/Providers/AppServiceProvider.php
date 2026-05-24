<?php

namespace App\Providers;

use App\Services\CompanySettingService;
use App\Services\SiteContentService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteContentService::class);
        $this->app->singleton(CompanySettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->ensureProductionUsesBuiltAssets();

        View::composer([
            'layouts.partials.site-footer',
            'layouts.partials.site-header',
            'layouts.guest',
            'layouts.auth',
            'components.site-brand',
            'components.marketing-layout',
            'components.layouts.user',
            'pages.contact',
            'layouts.statement',
            'statements.partials.document',
            'reports.transaction-statement',
        ], function ($view): void {
            try {
                $view->with('company', app(CompanySettingService::class)->get());
            } catch (\Throwable) {
                $view->with('company', null);
            }
        });
    }

    /**
     * Laravel loads assets from the Vite dev server when public/hot exists.
     * If that file is left behind after "npm run dev" stops, CSS/JS return 404.
     */
    private function ensureProductionUsesBuiltAssets(): void
    {
        $hot = public_path('hot');
        $manifest = public_path('build/manifest.json');

        if (! is_file($hot)) {
            return;
        }

        $useDevServer = (bool) env('VITE_USE_DEV', false);
        $forceBuild = $this->app->environment('production')
            || ! is_file($manifest)
            || ! $useDevServer;

        if ($forceBuild) {
            @unlink($hot);

            return;
        }

        if (! $this->viteDevServerReachable(trim((string) file_get_contents($hot)))) {
            @unlink($hot);
        }
    }

    private function viteDevServerReachable(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return false;
        }

        $port = (int) ($parts['port'] ?? 5173);
        $hosts = array_unique([
            $parts['host'] ?? '127.0.0.1',
            '127.0.0.1',
            'localhost',
        ]);

        foreach ($hosts as $host) {
            $errno = 0;
            $errstr = '';
            $socket = @fsockopen($host, $port, $errno, $errstr, 0.25);

            if ($socket !== false) {
                fclose($socket);

                return true;
            }
        }

        return false;
    }
}
