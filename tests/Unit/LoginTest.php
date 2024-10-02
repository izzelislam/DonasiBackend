<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
   
    //  write login api test 
    public function testLoginApi()
    {
      // Mock the HTTP response
      Http::fake([
          'api/login' => Http::response(['token' => 'fake-token'], 200)
      ]);

      // Make the HTTP request
      $response = Http::post('api/login', [
          'email' => 'test@example.com',
          'password' => 'password'
      ]);

      // Assert the response status
      $this->assertEquals(200, $response->status());

      // Assert the response structure
      $this->assertArrayHasKey('token', $response->json());
  }


}
