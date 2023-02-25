<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed(DatabaseSeeder::class));

/**
 * /api/posts tests
 */
it('returns a list of all posts from the users the authenticated user follows', function () 
{
  
});

it('should be ordered by the time the posts were created', function () 
{
   
});

it('should include the total number of likes it has received', function () 
{
   
});

/**
 * /api/posts/{id} tests
 */
it('returns a list of all posts from the users the authenticated user follows', function () 
{
  
});

it('should be ordered by the time the posts were created', function () 
{
   
});

it('should include the total number of likes it has received', function () 
{
   
});
