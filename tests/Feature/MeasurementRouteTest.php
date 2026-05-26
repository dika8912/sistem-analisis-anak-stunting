<?php

namespace Tests\Feature;

use Tests\TestCase;

class MeasurementRouteTest extends TestCase
{
    /**
     * Test if measurement create page is accessible.
     */
    public function test_measurement_create_page_returns_successful_response(): void
    {
        $response = $this->get('/measurements/create?child_id=1');

        $response->assertStatus(200);
        $response->assertViewIs('measurements.create');
        $response->assertViewHas('childId', '1');
    }
}
