<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Test dasar: tamu yang membuka "/" harus diarahkan ke halaman login.
 */
class ExampleTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
