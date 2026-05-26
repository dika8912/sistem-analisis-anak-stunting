<?php

namespace Tests\Feature;

use Tests\TestCase;

class EducationRouteTest extends TestCase
{
    /**
     * Test public education list page.
     */
    public function test_educations_index_page_returns_successful_response(): void
    {
        $response = $this->get('/educations');
        $response->assertStatus(200);
        $response->assertViewIs('educations.index');
    }

    /**
     * Test public education detail page.
     */
    public function test_educations_show_page_returns_successful_response(): void
    {
        $response = $this->get('/educations/1');
        $response->assertStatus(200);
        $response->assertViewIs('educations.show');
    }

    /**
     * Test admin education form page.
     */
    public function test_admin_educations_page_returns_successful_response(): void
    {
        $response = $this->get('/admin/educations');
        $response->assertStatus(200);
        $response->assertViewIs('educations.form');
    }
}
