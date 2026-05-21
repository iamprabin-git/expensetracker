<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteContent();
    }

    public function test_profile_route_redirects_to_settings(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertRedirect(route('settings.index', absolute: false));
    }

    public function test_settings_page_renders_redesigned_layout(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('settings.index'));

        $response->assertOk();
        $response->assertSee('settings-page', false);
        $response->assertSee('settings-panel', false);
        $response->assertSee('Profile photo', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('settings.index'))
            ->put(route('settings.profile.update'), [
                'name' => 'Test User',
                'phone' => '9800000000',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.index', absolute: false));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('9800000000', $user->phone);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('settings.account.destroy'), [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home', absolute: false));

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('settings.index'))
            ->delete(route('settings.account.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('accountDeletion', 'password')
            ->assertRedirect(route('settings.index', absolute: false));

        $this->assertNotNull($user->fresh());
    }
}
