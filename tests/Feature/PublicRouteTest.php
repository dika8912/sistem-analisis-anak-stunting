<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicRouteTest extends TestCase
{
    /**
     * Test if the homepage is accessible.
     */
    public function test_homepage_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('halaman.beranda');
    }

    /**
     * Test if the education page is accessible.
     */
    public function test_education_page_returns_successful_response(): void
    {
        $response = $this->get('/edukasi');

        $response->assertStatus(200);
        $response->assertViewIs('halaman.edukasi');
    }
}
