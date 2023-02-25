<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;
use App\Traits\ResponseHelper;
use App\Models\User;
use App\Models\Post;

uses(RefreshDatabase::class, ResponseHelper::class);

/**
 * GET /api/posts
 */
it('does not allow unathenticated user to view posts', function () {
    $response = $this->getJson('/api/posts');
    $response->assertStatus(self::UNAUTHORIZED_STATUS_CODE);
})->group('post');

it('fetches a list of all posts from the users the authenticated user follows', function () {
    $response = $this->getJson('/api/posts');
    $response->assertStatus(self::GET_STATUS_CODE);
})->group('post');

it('should be ordered by the time the posts were created', function () 
{
    $response = $this->getJson('/api/posts');
    $response->assertStatus(401);
})->group('post');

it('should include the total number of likes it has received', function () 
{
   
})->group('post');

/**
 * GET /api/posts/{id}
 */
it('should be able view any single post with the given ID', function () 
{
   
})->group('post');

/**
 * PUT/PATCH /api/posts
 */
it('should not be able to update anyone else\'s post', function () 
{
   
})->group('post');

it('should be able to update own post', function () 
{
   
})->group('post');

/**
 * DELETE /api/posts
 */
it('should not be able to delete anyone else\'s post', function () 
{
   
})->group('post');

it('should be able to delete own post', function () 
{
   
})->group('post');

/**
 * PATCH /api/posts/{id}/like
 */
it('should not allow unathenticated user to like a post', function () 
{
   
})->group('post');

it('should allow authenticated user to like a post', function () 
{
   
})->group('post');

/**
 * PATCH /api/posts/{id}/unlike
 */
it('should not allow unathenticated user to unlike a post', function () 
{
   
})->group('post');

it('should allow authenticated user to unlike a post', function () 
{
   
})->group('post');

/**
 * POST /api/users
 */
it('should allow authenticated user can create post', function () 
{
   
})->group('post');
