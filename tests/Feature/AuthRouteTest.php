<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthRouteTest extends TestCase
{
    /**
     * Test if login page is accessible.
     */
    public function test_login_page_returns_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test if register page is accessible.
     */
    public function test_register_page_returns_successful_response(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }
}
