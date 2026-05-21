<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteContent();
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('settings.index'))
            ->put(route('settings.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password-12',
                'password_confirmation' => 'new-password-12',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.index', absolute: false));

        $this->assertTrue(Hash::check('new-password-12', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('settings.index'))
            ->put(route('settings.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password-12',
                'password_confirmation' => 'new-password-12',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect(route('settings.index', absolute: false));
    }
}
