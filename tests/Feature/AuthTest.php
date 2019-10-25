<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Tymon\JWTAuth\Facades\JWTAuth;
use App\User;


class AuthTest extends TestCase
{
    public function testLoginReturnsValidToken(){
        $loginPayLoad = [
            'email' => 'test6@email.com',
            'password' => 'secretpwd'
        ];

        $expectedLoginResponseStructure = [
            'access_token',
            'token_type',
            'expires_in'
        ];

        $response = $this
            ->json('POST','/api/login', $loginPayLoad);

        $response
            ->assertStatus(201) //expect status 201 for authentication successful
            ->assertJsonStructure($expectedLoginResponseStructure);
    }

    public function tokenUsersCanSeePrivateApi()
    {
        $testUserEmail = 'test6@email.com';
        $user = User::where('email',$testUserEmail)->first();
        $token = JWTAuth::fromUser($user);

        $privateEndpointCheckPayload = [
            'token' => $token
        ];

        $expectedApiResponseJsonStructure = [
            'data' => [
                'message',
                'debug' => [
                    'original' => [
                        'user' => [
                            'id'
                        ],
                    ]
                ]
            ]
        ];

        $response = $this->json('GET','/api/closed', $privateEndpointCheckPayload);
        $response
            ->assertStatus(200)
            ->assertJsonStructure($expectedApiResponseJsonStructure)
            ->assertOk();
    }
}
