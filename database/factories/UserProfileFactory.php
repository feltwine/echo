<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::get()->random();

        return [
            'user_id' => $user->id,

            'display_name' => $user->user_name,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),

            'bio' => $this->faker->text(),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', null]),

            'avatar_path' => $this->faker->imageUrl(),
            'background_path' => $this->faker->imageUrl(),
        ];
    }
}
