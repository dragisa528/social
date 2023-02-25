<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Follower;
use App\Models\Like;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // user followers
        $followers = Follower::factory()
        ->count(1)
        ->state(function (array $attributes, User $user) {
            return [
                'follower_id' => $user->id
            ];
        });

        // user following
        $follows = Follower::factory()
        ->count(1)
        ->state(function (array $attributes, User $user) {
            return [
                'following_id' => $user->id
            ];
        });

        // post likes
        $likes = Like::factory()
        ->count(1)
        ->state(function (array $attributes, Post $post) {
            return [
                'post_id' => $post->id
            ];
        });

        // users posts 
        $posts = Post::factory()
        ->has($likes)
        ->count(2);

        $users = User::factory(1)
        ->has($posts)
        ->hasAttached($followers)
        ->hasAttached($follows)
        ->create();
    }
}
