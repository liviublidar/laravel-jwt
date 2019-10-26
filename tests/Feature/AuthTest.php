<?php

namespace Tests\Feature;

use App\Account;
use Mockery\Exception;
use Tests\TestCase;
use \Tymon\JWTAuth\Facades\JWTAuth;
use App\User;

define( 'TEST_USER', [
    'email' => 'test' . (string) random_int(1, 99999) . '@testmail.com',
    'password' => 'somestupidpwd'
]);
class AuthTest extends TestCase
{

    /**
     * try registering with valid data
     */
    public function testValidRegisterSucceeds()
    {
        $randomAccount = Account::getRandomAccount();
        $randomAccountCode = $randomAccount->code;
        $randomAccountId = $randomAccount->id;

        if (!is_numeric($randomAccountId)) {
          throw new Exception('Account id is not numeric');
        }

        $registerFormData = [
            'first_name' => 'testy',
            'last_name' => 'testtwo',
            'password' => TEST_USER['password'],
            'password_confirmation' => TEST_USER['password'],
            'email' => TEST_USER['email'],
            'dob' => '1991-02-03',
            'code' => $randomAccountCode
        ];

        $expectedApiResponseJsonStructure = [
            'access_token',
            'user' => [
                'id',
                'account_id'
            ]

        ];

        $response = $this->post('/api/register', $registerFormData);
        $response
            ->assertStatus(201)
            ->assertJsonStructure($expectedApiResponseJsonStructure)
            ->assertJsonFragment(['account_id' => $randomAccountId]);
    }

    /**
     * register with empty values in all fields
     */
    public function testEmptyRegisterFails()
    {
        $registerFormData = [
            'first_name' => '',
            'last_name' => '',
            'password' => '',
            'password_confirmation' => '',
            'email' => '',
            'dob' => '',
            'code' => ''
        ];

        $expectedApiResponseFragments = [];

        //loop through the request data keys and take out passwordm confirmation since that one is not
        //individually validated and does not throw an error on itself
        foreach (array_diff(array_keys($registerFormData), ['password_confirmation']) as $registerKey) {
            $validatorErrorMessage = 'The '
                . str_replace('_', ' ', $registerKey)
                . ' field is required.';
            $expectedApiResponseFragments[$registerKey][] = $validatorErrorMessage;
        }

        $response = $this->post('/api/register', $registerFormData);
        $response
            ->assertStatus(400)
            ->assertJsonFragment($expectedApiResponseFragments);
    }

    /**
     * register with wrong org code
     */
    public function testWrongOrgCodeRegisterFails()
    {
        $registerFormData = [
            'first_name' => 'testy',
            'last_name' => 'testtwo',
            'password' => TEST_USER['password'],
            'password_confirmation' => TEST_USER['password'],
            'email' => 'another'.TEST_USER['email'],
            'dob' => '1991-02-03',
            'code' => 'the org code could randomly becomes this string'
        ];

        $response = $this->post('/api/register', $registerFormData);
        $response->assertStatus(400);
    }

    /**
     * normal login with valid credentials returns a proper token
     */
    public function testLoginReturnsValidToken()
    {
        $expectedLoginResponseStructure = [
            'access_token',
            'token_type',
            'expires_in'
        ];

        $response = $this->json('POST','/api/login', TEST_USER);
        $response
            ->assertStatus(201) //expect status 201 for authentication successful
            ->assertJsonStructure($expectedLoginResponseStructure);
    }

    public function testWrongLoginReturnsUnauthorized()
    {
        $loginPayLoad = [
            'email' => 'abdas@asiasd.com',
            'password' => 'secretaspwd'
        ];


        $response = $this->json('POST','/api/login', $loginPayLoad);
        $response->assertStatus(401); //expect 401 for unauthorized request
    }

    /**
     * check the availablility of private api for valid token users
     */
    public function testTokenUsersCanSeePrivateApi()
    {
        $user = User::getRandomUser();
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

    /**
     * deny access for users that don't pass a token
     */
    public function testNonTokenUsersAreBlockedFromProtectedApi()
    {
        $privateEndpointCheckPayload = [
            'notAToken' => 'abcdefg'
        ];
        $response = $this->json('GET','/api/closed', $privateEndpointCheckPayload);
        $response->assertStatus(401);
    }

    /**
     * deny access for users that pass a wrong token
     */
    public function testWrongTokenUsersAreBlockedFromProtectedApi()
    {
        $privateEndpointCheckPayload = [
            'token' => 'abcdefg'
        ];
        $response = $this->json('GET','/api/closed', $privateEndpointCheckPayload);
        $response->assertStatus(401);
    }
}
