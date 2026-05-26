<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminSearchRouteTest extends TestCase
{
    /**
     * Test if admin children search page is accessible.
     */
    public function test_admin_search_page_returns_successful_response(): void
    {
        $response = $this->get('/admin/children');

        $response->assertStatus(200);
        $response->assertViewIs('admin.search');
    }
}
