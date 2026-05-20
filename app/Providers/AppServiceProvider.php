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
        View::composer([
            'layouts.partials.site-footer',
            'layouts.partials.site-header',
            'components.site-brand',
            'components.marketing-layout',
            'components.layouts.user',
            'pages.contact',
        ], function ($view): void {
            try {
                $view->with('company', app(CompanySettingService::class)->get());
            } catch (\Throwable) {
                $view->with('company', null);
            }
        });
    }
}
