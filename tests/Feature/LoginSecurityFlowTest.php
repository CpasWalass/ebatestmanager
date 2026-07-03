<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginSecurityFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_login_with_temporary_password_redirects_to_the_profile_page(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('temp-password'),
            'must_change_password' => true,
            'temporary_password_hash' => Hash::make('temp-password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'temp-password',
        ]);

        $response->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);
    }

    public function test_account_is_locked_after_five_failed_login_attempts(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
            'failed_login_attempts' => 4,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->from('/login')->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $user->refresh();

        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->isFuture());
        $this->assertGuest();
    }

    public function test_locked_account_shows_a_visible_message_on_the_login_page(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
            'locked_until' => now()->addMinutes(30),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $response->assertSessionHas('lockout_message', 'Votre compte est temporairement bloqué. Veuillez réessayer dans 30 minutes.');
    }
}
