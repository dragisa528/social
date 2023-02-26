<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);

/**
 * POST /api/token/fetch
 */
it('can fetch valid api token with username and password', function () 
{
    $email    = 'jane@test.com';
    $password = 'password';

    $jane = User::factory()->create([
        'email'    => $email,
        'password' => Hash::make($password)
    ]);

    $response = $this->postJson('/api/token/fetch',[
        'email'    => $email,
        'password' => $password
    ]);

    $token = $response->decodeResponseJson()['token'];
    $userId = explode('|', $token)[0];
    assertEquals($userId, $jane->id);

    // can access protected endpoint with token
    $response = $this
    ->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])
    ->getJson("/api/users/{$jane->id}");

    $user = $response->decodeResponseJson()['data'];
    expect($user)
    ->toMatchArray(['id' => $jane->id, 'email' => $jane->email]);


})->group('token', 'token-can-fetch-token-with-email-password');