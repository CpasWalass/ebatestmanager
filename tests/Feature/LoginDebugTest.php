<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginDebugTest extends TestCase
{
    public function test_users_route()
    {
        $user = User::where('email', 'chef@ebatest.local')->first();
        if (!$user) {
            $this->markTestSkipped('Chef not found');
        }

        $response = $this->actingAs($user)->get('/users');
        $response->dumpHeaders();
        $response->dumpSession();
        $response->assertStatus(200);
    }
}
