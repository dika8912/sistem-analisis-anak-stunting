<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChildRouteTest extends TestCase
{
    /**
     * Test if child create page is accessible.
     */
    public function test_child_create_page_returns_successful_response(): void
    {
        $response = $this->get('/children/create');

        $response->assertStatus(200);
        $response->assertViewIs('children.create');
    }

    /**
     * Test if child detail page is accessible.
     */
    public function test_child_detail_page_returns_successful_response(): void
    {
        $response = $this->get('/children/1');

        $response->assertStatus(200);
        $response->assertViewIs('children.show');
        $response->assertViewHas('id', '1');
    }
}
