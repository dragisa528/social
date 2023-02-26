<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // users
        $emeka = User::factory()->create([
            'name'  => 'Emeka Okafor ID - 1',
            'email' => 'emeka@test.com'
        ]);

        $taylor = User::factory()->create([
            'name'  => 'John Doe ID - 2',
            'email' => 'john@test.com'
        ]);

        $simon = User::factory()->create([
            'name'  => 'Simon Doe ID - 3',
            'email' => 'simon@test.com'
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

        $simonPost5 = $simon->posts()->create([
            'content' => 'Simon Post ID - 5'
        ]);

        $taylorPost3->setCreatedAt(\Carbon\Carbon::yesterday());
        $taylorPost3->save();

        // follows
        $emeka->follow($taylor);
        $simon->follow($emeka);
        $emeka->follow($simon);

        // likes
        $emeka->likePost($taylorPost3);
    }
}
