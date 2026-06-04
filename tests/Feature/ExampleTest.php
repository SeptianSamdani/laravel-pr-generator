<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root URL redirects to login when unauthenticated.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Route '/' memerlukan autentikasi, maka redirect ke login adalah perilaku yang benar
        $response->assertStatus(302);
    }
}
