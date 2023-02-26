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
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);

    Sanctum::actingAs($jane);

    // when jane view John's profile
    $response = $this->getJson("/api/users/{$john->id}");
    $response->assertOk();
    $johnProfile = $response->decodeResponseJson()['data'];

    expect($johnProfile)->toMatchArray(['id' => $john->id, 'name' => $john->name]);
    expect($johnProfile)->not->toMatchArray(['email' => $john->email, 'total_followers' => 0, 'total_follows' => 0]);

    // when jane view own profile
    $response = $this->getJson("/api/users/{$jane->id}");
    $response->assertOk();
    $janeProfile = $response->decodeResponseJson()['data'];

    expect($janeProfile)->toMatchArray(
        ['id' => $jane->id, 'name' => $jane->name, 'email' => $jane->email, 'total_followers' => 0, 'total_follows' => 0]
    );

})->group('user', 'user-show-more-details-for-only-auth-user');

it('does not shows email, follower and following counts for others profile with the given ID', function () 
{
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);

    Sanctum::actingAs($jane);
    $response = $this->getJson("/api/users/{$john->id}");
    $response->assertOk();
    $profile = $response->decodeResponseJson()['data'];

    // assert jane's cannot view john's email, followers and follows
    expect($profile)
    ->not
    ->toMatchArray(
        [$john->name, 'email' => $john->email, 'total_followers' => 0, 'total_follows' => 0]
    );

    // assert jane can see basic info like name
    expect($profile)->toMatchArray(['id' => $john->id, 'name' => $john->name]);

})->group('user', 'user-cannot-view-others-email-followers-follows');

/**
 * PATCH /api/users/{id}/follow
 */
it('should not allow unathenticated user to follow a user', function () 
{
    $jane = User::factory()->create();

    $response = $this->patchJson("/api/users/{$jane->id}/follow");
    $response->assertStatus(ResponseHelper::UNAUTHORIZED);
    
    $this->assertDatabaseMissing('followers', ['following_id' => $jane->id]);  
})->group('user', 'user-unauthenticated-user-cannot-follow');

it('should allow authenticated user to follow a user', function () 
{
    $jane = User::factory()->create();
    $john = User::factory()->create();

    Sanctum::actingAs($jane);
    $response = $this->patchJson("/api/users/{$john->id}/follow");
    $response->assertStatus(ResponseHelper::NO_CONTENT);  

    $this->assertDatabaseHas('followers', ['following_id' => $john->id, 'follower_id' => $jane->id]);  

})->group('user', 'user-authenticated-user-can-follow');

it('should not allow authenticated user to follow their self', function () 
{
    $jane = User::factory()->create();

    Sanctum::actingAs($jane);
    $response = $this->patchJson("/api/users/{$jane->id}/follow");
    $response->assertStatus(ResponseHelper::FORBIDDEN);  

    $this->assertDatabaseMissing('followers', ['following_id' => $jane->id, 'follower_id' => $jane->id]);  
})->group('user', 'user-authenticated-user-cannot-follow-self');

/**
 * PATCH /api/users/{id}/unfollow
 */
it('should not allow unathenticated user to unfollow a user', function () 
{
    $jane = User::factory()->create();
    $response = $this->patchJson("/api/users/{$jane->id}/unfollow");
    $response->assertStatus(ResponseHelper::UNAUTHORIZED);
    
    $this->assertDatabaseMissing('followers', ['following_id' => $jane->id]);  
})->group('user', 'user-unauthenticated-user-cannot-unfollow');

it('should allow authenticated user to unfollow a user', function () 
{
    $jane = User::factory()->create();
    $john = User::factory()->create();

    //Jane start following John
    $jane->follow($john);
    $this->assertDatabaseHas('followers', ['following_id' => $john->id, 'follower_id' => $jane->id]); 

    Sanctum::actingAs($jane);
    $response = $this->patchJson("/api/users/{$john->id}/unfollow");
    $response->assertStatus(ResponseHelper::NO_CONTENT);  

    $this->assertDatabaseMissing('followers', ['following_id' => $john->id, 'follower_id' => $jane->id]); 
})->group('user', 'user-authenticated-user-can-unfollow');

it('should not allow authenticated user to unfollow their self', function () 
{
    $jane = User::factory()->create();

    Sanctum::actingAs($jane);
    $response = $this->patchJson("/api/users/{$jane->id}/unfollow");
    $response->assertStatus(ResponseHelper::FORBIDDEN);   
})->group('user', 'user-authenticated-user-cannot-unfollow-self');