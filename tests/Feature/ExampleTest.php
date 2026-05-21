<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteContent();
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-label="Breadcrumb"', false);
        $response->assertSee(route('home'), false);
    }

    public function test_faq_page_renders_redesigned_faq_section(): void
    {
        $response = $this->get(route('faq'));

        $response->assertOk();
        $response->assertSee('faq-section', false);
        $response->assertSee('Search questions', false);
        $response->assertSee('Contact support', false);
    }

    public function test_contact_page_renders_redesigned_contact_section(): void
    {
        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('contact-section', false);
        $response->assertSee('Send a message', false);
        $response->assertSee('contact-name', false);
    }

    public function test_pricing_page_renders_redesigned_pricing_section(): void
    {
        $response = $this->get(route('pricing'));

        $response->assertOk();
        $response->assertSee('pricing-section', false);
        $response->assertSee('pricing-card--featured', false);
        $response->assertSee('Get started', false);
    }
}
