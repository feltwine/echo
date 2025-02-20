<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'parent_id' => null,
            'body' => $this->faker->paragraph(),
            'image' => null,
            'vote_count' => $this->faker->numberBetween(-50, 500),
        ];
    }

    public function withParent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Comment::factory(),
            ];
        });
    }
}
