<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\Like;

uses(RefreshDatabase::class);

/**
 * GET /api/posts
 */
it('does not allow unathenticated user to view posts', function () {
    $response = $this->getJson('/api/posts');
    $response->assertStatus(responseHelper::UNAUTHORIZED);
})->group('post', 'post-unauthenticated-disallowed');

it('fetches a list of all posts from the users the authenticated user follows', function () 
{
    // create users
    $jane = User::factory()->create();
    $john = User::factory()->create();
    $mike = User::factory()->create();

    // create posts
    $janePost = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 
    $johnPost = $john->posts()->create(['content' => 'John Post ID - 2']);
    $mikePost = $mike->posts()->create(['content' => 'Mike Post ID - 2']);

    // Mike follows Jane and John, so we can return posts from users mike is following
    $mike->follow($jane);
    $mike->follow($john);

    // login as mike so we can fetch posts from users he is following
    Sanctum::actingAs($mike);

    $response = $this->getJson('/api/posts');
    $response->assertOk();
    $content = $response->decodeResponseJson()['data'];

    expect($content)->toHaveCount(2); 
    expect($content[0])->toMatchArray(['id' => $janePost->id, 'user_id' => $jane->id]);
    expect($content[1])->toMatchArray(['id' => $johnPost->id, 'user_id' => $john->id]);
})->group('post', 'post-from-followed-users');

it('should be ordered by the time the posts were created', function () 
{
    // create users
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);
    $mike = User::factory()->create(['name' => 'Mike Doe ID - 3', 'email' => 'mike.doe@test.com']);

    // create posts
    $janePost = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 
    $johnPost = $john->posts()->create(['content' => 'John Post ID - 2']);
    $mikePost = $mike->posts()->create(['content' => 'Mike Post ID - 2']);

    // we set johns post to tomorrow and we expect it to be first in the list
    $johnPost->setCreatedAt(Carbon::tomorrow());
    $johnPost->save();

    // Mike follows Jane and John, so we can return posts from users mike is following
    $mike->follow($jane);
    $mike->follow($john);

    // login as mike so we can fetch posts from users he is following
    Sanctum::actingAs($mike);

    $response = $this->getJson('/api/posts');
    $response->assertOk();

    $content = $response->decodeResponseJson()['data'];
    expect($content[0])->toMatchArray(['id' => $johnPost->id, 'user_id' => $john->id]);

})->group('post', 'post-order-created_at');

it('should include the total number of likes it has received', function () 
{
    $jane = User::factory()->create(['name' => 'Jane Doe ID - 1', 'email' => 'jane.doe@test.com']);
    $john = User::factory()->create(['name' => 'John Doe ID - 2', 'email' => 'john.doe@test.com']);

    $janePost = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 
    $john->follow($jane);
    $john->likePost($janePost);

    Sanctum::actingAs($john);

    $response = $this->getJson('/api/posts');
    $response->assertOk();
    $content = $response->decodeResponseJson()['data'];

    expect($content[0])->toMatchArray(['total_likes' => 1]);
})->group('post', 'post-check-number-of-likes');

/**
 * GET /api/posts/{id}
 */
it('should be able view any single post with the given ID', function () 
{
    $jane = User::factory()->create();
    $john = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    Sanctum::actingAs($john);

    $response = $this->getJson('/api/posts/' . $post->id);
    $response->assertOk();
 
    $data = $response->decodeResponseJson()['data'];
    expect($data)->toMatchArray(['id' => $post->id, 'user_id' => $jane->id]);

})->group('post', 'post-view-any-single-post');

/**
 * PUT/PATCH /api/posts
 */
it('should not be able to update anyone else\'s post', function () 
{
    $jane = User::factory()->create();
    $john = User::factory()->create();

    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    // John cannot update Jane's post
    Sanctum::actingAs($john);
    $response = $this->putJson("/api/posts/{$post->id}", ['content' => 'Hacked you!']);
    $response->assertStatus(ResponseHelper::FORBIDDEN);
    $this->assertDatabaseMissing('posts', ['id' => $post->id, 'content' => 'Hacked you!']);  
})->group('post', 'post-cannot-update-others-post');

it('should be able to update own post', function () 
{
    $jane = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    // Jane can update own post
    Sanctum::actingAs($jane);
    $response = $this->putJson("/api/posts/{$post->id}", ['content' => 'More interesting content']);

    $response->assertStatus(ResponseHelper::NO_CONTENT);
    $this->assertDatabaseHas('posts', ['id' => $post->id, 'content' => 'More interesting content']);  
})->group('post', 'post-can-update-own-post');

/**
 * DELETE /api/posts
 */
it('should not be able to delete others post', function () 
{
    $jane = User::factory()->create();
    $john = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    // John cannot delete Jane's post
    Sanctum::actingAs($john);
    $response = $this->deleteJson("/api/posts/{$post->id}", ['content' => 'Hacked you!']);

    $response->assertStatus(ResponseHelper::FORBIDDEN);
    $this->assertDatabaseHas('posts', ['id' => $post->id, 'user_id' => $post->id]);  
})->group('post', 'post-cannot-delete-others-post');

it('should be able to delete own post', function () 
{
    $jane = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    // Jane can delete own post
    Sanctum::actingAs($jane);
    $response = $this->deleteJson("/api/posts/{$post->id}", [
        'content' => 'More interesting content'
    ]);
    $response->assertStatus(ResponseHelper::NO_CONTENT);
    $this->assertDatabaseMissing('posts', ['id' => $post->id, 'content' => 'More interesting content']);  
})->group('post', 'post-can-delete-own-post');

/**
 * PATCH /api/posts/{id}/like
 */
it('should not allow unathenticated user to like a post', function () 
{
    $jane = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    $response = $this->patchJson("/api/posts/{$jane->id}/like");
    $response->assertStatus(ResponseHelper::UNAUTHORIZED);
    
    $this->assertDatabaseMissing('likes', ['user_id' => $jane->id, 'post_id' => $post->id]);  
})->group('post', 'post-cannot-like-post-when-not-authenticated');

it('should allow authenticated user to like a post', function () 
{
    $jane = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    Sanctum::actingAs($jane);
    $response = $this->patchJson("/api/posts/{$post->id}/like");
    $response->assertStatus(ResponseHelper::NO_CONTENT);  
})->group('post', 'post-can-like-post-when-authenticated');

/**
 * PATCH /api/posts/{id}/unlike
 */
it('should not allow unathenticated user to unlike a post', function () 
{
    $jane = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    $response = $this->patchJson("/api/posts/{$post->id}/unlike");
    $response->assertStatus(ResponseHelper::UNAUTHORIZED);
    
    $this->assertDatabaseMissing('likes', ['user_id' => $jane->id, 'post_id' => $post->id]);  
})->group('post', 'post-cannot-unlike-post-when-not-authenticated');

it('should allow authenticated user to unlike a post', function () 
{
    $jane = User::factory()->create();
    $post = $jane->posts()->create(['content' => 'Jane Post ID - 1']); 

    // like
    $like = Like::factory()->create(['user_id' => $jane->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('likes', ['user_id' => $jane->id, 'post_id' => $post->id]); 

    Sanctum::actingAs($jane);
    $response = $this->patchJson("/api/posts/{$post->id}/unlike");
    $response->assertStatus(ResponseHelper::NO_CONTENT);  

    $this->assertDatabaseMissing('likes', ['user_id' => $jane->id, 'post_id' => $post->id]);  
})->group('post', 'post-can-unlike-post-when-authenticated');