<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');
 
        $response->assertStatus(200);
    }



   public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
 
        $response->assertStatus(200);
    }

  
    
}
