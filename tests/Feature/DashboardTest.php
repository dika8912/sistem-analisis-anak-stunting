<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * Test if dashboard page is accessible and returns the correct view.
     */
    public function test_dashboard_page_is_accessible()
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
    }
}
