<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed(DatabaseSeeder::class));

it('returns a list of all posts from the users the authenticated user follows', function () 
{
  
});

test('', function () 
{
   
});