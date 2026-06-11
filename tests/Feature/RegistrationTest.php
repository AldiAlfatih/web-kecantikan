<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'address' => 'Test Address',
            'phone' => '1234567890',
            'skin_type' => 'Normal',
            'skin_tone' => 'Fair',
            'undertone' => 'Warm',
            'skin_problem' => 'Jerawat',
            'vein_color' => 'Biru',
        ];

        $response = $this->post('/daftar', $userData);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }
}