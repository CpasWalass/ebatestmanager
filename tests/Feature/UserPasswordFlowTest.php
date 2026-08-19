<?php

namespace Tests\Feature;

use App\Livewire\TeamManager;
use App\Livewire\UserProfile;
use App\Mail\WelcomeNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPasswordFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);
    }

    public function test_creating_a_user_sends_a_temporary_password_and_requires_first_change(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('chef_project');
        $admin->givePermissionTo('manage users');

        $this->actingAs($admin);

        Livewire::test(TeamManager::class)
            ->set('name', 'Alice Example')
            ->set('email', 'alice@example.com')
            ->set('role', 'tester')
            ->call('save');

        $user = User::where('email', 'alice@example.com')->firstOrFail();

        $this->assertTrue((bool) $user->must_change_password);
        $this->assertNotNull($user->temporary_password_hash);

        Mail::assertSent(WelcomeNewUser::class, function (WelcomeNewUser $mail) use ($user): bool {
            $this->assertSame($user->email, $mail->user->email);
            $this->assertNotEmpty($mail->temporaryPassword);
            $this->assertTrue(Hash::check($mail->temporaryPassword, $user->temporary_password_hash));

            return true;
        });
    }

    public function test_password_change_requires_temporary_password_when_must_change(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('temp-password'),
            'must_change_password' => true,
            'temporary_password_hash' => Hash::make('temp-password'),
        ]);

        $this->actingAs($user);

        Livewire::test(UserProfile::class)
            ->set('temporary_password', 'wrong-temp')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertHasErrors(['temporary_password']);
    }

    public function test_password_change_requires_current_password_when_not_must_change(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
            'must_change_password' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(UserProfile::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }
}
