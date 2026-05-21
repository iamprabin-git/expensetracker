<?php

namespace Tests;

use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\SitePageSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            PreventRequestForgery::class,
        ]);
    }

    protected function seedSiteContent(): void
    {
        $this->seed([
            SitePageSeeder::class,
            CompanySettingSeeder::class,
        ]);
    }
}
