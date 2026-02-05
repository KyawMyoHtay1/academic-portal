<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTermsSimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_terms()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test_terms@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => false,
        ]);

        $response->assertSessionHasErrors('terms');
    }

    public function test_registration_succeeds_with_terms()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test_terms@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
