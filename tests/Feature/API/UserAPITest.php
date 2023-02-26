<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\Post;

uses(RefreshDatabase::class);

/**
 * GET /api/users
 */
it('does not allow unathenticated user to view users', function () {
    $response = $this->getJson("/api/users");
    $response->assertStatus(ResponseHelper::UNAUTHORIZED);
})->group('user');

it('should allow authenticated user to view all users', function () {
    $response = $this->getJson("/api/users");
    $response->assertStatus(ResponseHelper::OK);
})->group('user');

it('does not show email, follower and following counts for other profiles', function () 
{
    $response = $this->getJson("/api/users");
    $response->assertStatus(responseHelper::OK);
})->group('user');

it('shows email, follower and following counts for authenticated profile', function () 
{
    $response = $this->getJson("/api/users");
    $response->assertStatus(responseHelper::OK);
})->group('user');

/**
 * GET /api/users/{id}
 */
it('should be able view any single user with the given ID', function () 
{
    $user = User::factory()->create();

    $response = $this->getJson("/api/users/{$user->id}");
})->group('user');

it('shows email, follower and following counts for authenticated profile with the given ID', function () 
{
    $user = User::factory()->create();
    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertStatus(responseHelper::OK);
})->group('user');

it('does not shows email, follower and following counts for others profile with the given ID', function () 
{
    $user = User::factory()->create();
    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertStatus(responseHelper::OK);
})->group('user');

/**
 * PATCH /api/users/{id}/follow
 */
it('should not allow unathenticated user to follow a user', function () 
{
    $user = User::factory()->create();
    $response = $this->putJson("/api/users/{$user->id}/follow");
    
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('user');

it('should allow authenticated user to follow a user', function () 
{
    $user = User::factory()->create();
    $response = $this->putJson("/api/users/{$user->id}/follow");
    
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('user');

it('should not allow authenticated user to follow their self', function () 
{
    $user = User::factory()->create();
    $response = $this->putJson("/api/users/{$user->id}/follow");
    
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('user');

/**
 * PATCH /api/users/{id}/unfollow
 */
it('should not allow unathenticated user to unfollow a user', function () 
{
    $user = User::factory()->create();
    $response = $this->putJson("/api/users/{$user->id}/unfollow");
    
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('user');

it('should allow authenticated user to unfollow a user', function () 
{
    $user = User::factory()->create();
    $response = $this->putJson("/api/users/{$user->id}/unfollow");
    
    $response->assertStatus(responseHelper::NO_CONTENT);
})->group('user');

it('should not allow authenticated user to unfollow their self', function () 
{
    $user = User::factory()->create();
    $response = $this->putJson("/api/users/{$user->id}/follow");
    
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('user');