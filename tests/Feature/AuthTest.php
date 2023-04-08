<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
   public function a_user_can_login_using_api()
   {
       $john = create(User::class);

       $response = $this->articleJson(route("auth.login"), [
           "email" => $john->email,
           "password" => "password"
       ]);

       $jsonResponse = $response->json();

       $this->assertEquals($john->id, auth()->user()->id);
       $this->assertNotNull($jsonResponse["access_token"]);
   }

    /** @test */
    public function a_user_can_logout_using_api()
    {
        $john = create(User::class);

        $response = $this->articleJson(route("auth.login"), [
            "email" => $john->email,
            "password" => "password"
        ]);

        $jsonResponse = $response->json();

        $this->withHeaders([
                "Authorization" => "Bearer ". $jsonResponse["access_token"]
            ])
            ->articleJson(route("auth.logout"));

        $this->assertNull(auth()->user());
    }

}
