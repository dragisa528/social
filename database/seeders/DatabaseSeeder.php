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
        // users
        $emeka = User::factory()->create([
            'name' => 'Emeka Okafor ID - 1'
        ]);

        $taylor = User::factory()->create([
            'name' => 'Taylor Otwel ID - 2'
        ]);

        $simon = User::factory()->create([
            'name' => 'Simon Finx ID - 3'
        ]);

        // posts
        $emekaPost1 = $emeka->posts()->create([
            'content' => 'Emeka Post ID - 1'
        ]);
        $emekaPost2 = $emeka->posts()->create([
            'content' => 'Emeka Post ID - 2'
        ]);

        $taylorPost3 = $taylor->posts()->create([
            'content' => 'Taylor Post ID - 3'
        ]);
        $taylorPost4 = $taylor->posts()->create([
            'content' => 'Taylor Post ID - 4'
        ]);



        // dd($taylorPost2->author);
        
        // emeka started following taylor
        $emeka->follow($taylor);
        $simon->follow($emeka);


        dd($emeka->followers->first()->name);






        // $emeka->follow($taylor);


        // $emeka->follow($taylor);
        // $emeka->unfollow($taylor);
        // $emeka->isFollowing($taylor);

        // $user1->followers()->attach($user2);



        // dd($emeka->followers()->where('follower_id', $taylor)->count());
      

        // // user followers
        // $followers = Follower::factory()
        // ->count(1)
        // ->state(function (array $attributes, User $user) {
        //     return [
        //         'follower_id' => $user->id
        //     ];
        // });

        // // user following
        // $follows = Follower::factory()
        // ->count(1)
        // ->state(function (array $attributes, User $user) {
        //     return [
        //         'following_id' => $user->id
        //     ];
        // });

        // // post likes
        // $likes = Like::factory()
        // ->count(1)
        // ->state(function (array $attributes, Post $post) {
        //     return [
        //         'post_id' => $post->id
        //     ];
        // });

        // // users posts 
        // $posts = Post::factory()
        // ->has($likes)
        // ->count(2);

        // $users = User::factory(1)
        // ->has($posts)
        // ->hasAttached($followers)
        // ->hasAttached($follows)
        // ->create();
    }
}
