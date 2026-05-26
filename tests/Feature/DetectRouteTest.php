<?php

namespace Tests\Feature;

use Tests\TestCase;

class DetectRouteTest extends TestCase
{
    /**
     * Test if detect public page is accessible.
     */
    public function test_detect_page_returns_successful_response(): void
    {
        $response = $this->get('/detect');

        $response->assertStatus(200);
        $response->assertViewIs('detect.index');
    }

    /**
     * Test if detect print endpoint works.
     */
    public function test_detect_print_generates_pdf(): void
    {
        $payload = [
            'age_months' => 24,
            'gender' => 'L',
            'height' => 85.5,
            'weight' => 12.3,
            'z_score' => -1.2,
            'status' => 'Normal',
            'ml_prediction' => 'Aman',
        ];

        $response = $this->post('/detect/print', $payload);

        $response->assertStatus(200);
        // Memastikan header mengindikasikan file PDF (application/pdf)
        $response->assertHeader('content-type', 'application/pdf');
    }
}
