<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginDebugTest extends TestCase
{
    public function test_login()
    {
        $response = $this->post('/login', [
            'email' => 'chef@ebatest.local',
            'password' => 'password',
        ]);
        
        $response->dump();
        $response->dumpSession();
    }
}
