<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Hub;
use App\Models\Post;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(250)
            ->has(UserProfile::factory())
            ->create();

        Hub::factory(10)
            ->recycle($users) // Reuse existing users instead of creating new ones
            ->has(
                Post::factory(rand(10, 20))
                    ->recycle($users)
                    ->has(
                        Comment::factory(rand(0, 10))
                            ->recycle($users)
                    )
            )
            ->afterCreating(function (Hub $hub) use ($users) {
                $hub->followers()->attach($users->random(rand(1, $users->count())));
            })
            ->create();
    }
}
