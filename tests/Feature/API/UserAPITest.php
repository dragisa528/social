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
it('does not allow unathenticated user to view users', function () 
{
    $response = $this->getJson('/api/posts');
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('user', 'user-unauthenticated-cannot-view-users');

it('should allow authenticated user to view all users', function () 
{
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);
    $mike = User::factory()->create(['name' => 'Mike Doe ID - 3', 'email' => 'mike.doe@test.com']);

    Sanctum::actingAs($jane);
    $response = $this->getJson("/api/users");
    $response->assertOk();
    $content = $response->decodeResponseJson()['data'];

    expect($content)->toHaveCount(3); 
    expect($content[0])->toMatchArray(['id' => $jane->id, 'name' => $jane->name]);
    expect($content[1])->toMatchArray(['id' => $john->id, 'name' => $john->name]);
    expect($content[2])->toMatchArray(['id' => $mike->id, 'name' => $mike->name]);
})->group('user', 'user-authenticated-can-view-users');

it('shows email, follower and following counts for only authenticated profile', function () 
{
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);
    $mike = User::factory()->create(['name' => 'Mike Doe ID - 3', 'email' => 'mike.doe@test.com']);

    Sanctum::actingAs($jane);
    $response = $this->getJson("/api/users");
    $response->assertOk();
    $content = $response->decodeResponseJson()['data'];

    expect($content)->toHaveCount(3); 

    // assert jane's (currently authenticated user) profile contains all her details 
    expect($content[0])
    ->toMatchArray(
        ['id' => $jane->id, 'name' => $jane->name, 'email' => $jane->email, 'total_followers' => 0, 'total_follows' => 0]
    );

    // assert these fields are not in other users profile
    expect($content[1])->not->toMatchArray(['email' => $john->email, 'total_followers' => 0, 'total_follows' => 0]);
    expect($content[2])->not->toMatchArray(['email' => $mike->email, 'total_followers' => 0, 'total_follows' => 0]);

    expect($content[1])->toMatchArray(['id' => $john->id, 'name' => $john->name]);
    expect($content[2])->toMatchArray(['id' => $mike->id, 'name' => $mike->name]);

})->group('user', 'user-do-not-show-others-email-followers-follows');

/**
 * GET /api/users/{id}
 */
it('should be able view any single user with the given ID', function () 
{
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);

    Sanctum::actingAs($jane);
    $response = $this->getJson("/api/users/{$john->id}");

    $response->assertOk();
    $profile = $response->decodeResponseJson()['data'];

    expect($profile)->toMatchArray(['id' => $john->id, 'name' => $john->name]);
    expect($profile)->not->toMatchArray(['email' => $john->email, 'total_followers' => 0, 'total_follows' => 0]);
})->group('user', 'user-can-view-any-user-with-id');

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