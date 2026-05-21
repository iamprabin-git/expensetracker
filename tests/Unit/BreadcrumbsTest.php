<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\Breadcrumbs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteContent();
    }

    public function test_home_route_returns_single_current_crumb(): void
    {
        $this->get(route('home'));

        $items = Breadcrumbs::resolve();

        $this->assertCount(1, $items);
        $this->assertSame('Home', $items[0]['label']);
        $this->assertNull($items[0]['url']);
    }

    public function test_features_route_returns_home_link_and_current_page(): void
    {
        $this->get(route('features'));

        $items = Breadcrumbs::resolve();

        $this->assertSame('Home', $items[0]['label']);
        $this->assertSame(route('home'), $items[0]['url']);
        $this->assertSame('Features', $items[1]['label']);
        $this->assertNull($items[1]['url']);
    }

    public function test_dashboard_route_includes_dashboard_trail(): void
    {
        $user = User::factory()->create([
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->actingAs($user)->get(route('dashboard'));

        $items = Breadcrumbs::resolve();

        $this->assertSame('Home', $items[0]['label']);
        $this->assertSame('Dashboard', $items[2]['label']);
        $this->assertNull($items[2]['url']);
        $this->assertSame(route('dashboard'), $items[1]['url']);
    }
}
