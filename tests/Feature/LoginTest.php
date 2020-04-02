<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    /** @test */
    public function testLogin()
    {
        $data = array(
            'username' => 'admin@crater.in',
            'password' => 'admin@123'
        );

        $response = $this->json('POST', 'api/auth/login', $data);

        $response->assertOk();
    }
}
