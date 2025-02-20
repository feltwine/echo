<?php

namespace Database\Factories;

use App\Models\Hub;
use App\Models\User;
use App\Models\Enums\HubStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HubFactory extends Factory
{
    protected $model = Hub::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(HubStatus::cases()),
            'background_color' => $this->faker->hexColor(),
            'avatar_path' => null,
            'background_path' => null,
        ];
    }
}

