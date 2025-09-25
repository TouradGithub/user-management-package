<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tourad\UserManager\Models\UserType;

class UserTypeFactory extends Factory
{
    protected $model = UserType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'permissions' => ['view-profile', 'edit-profile'],
            'restrictions' => [],
            'meta_data' => [
                'max_storage' => $this->faker->randomElement(['1GB', '5GB', '10GB']),
                'max_uploads' => $this->faker->numberBetween(100, 1000),
            ],
            'is_active' => $this->faker->boolean(80),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'icon' => $this->faker->randomElement(['fas fa-user', 'fas fa-user-cog', 'fas fa-user-tie']),
            'color' => $this->faker->hexColor(),
        ];
    }

    /**
     * Indicate that the user type is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the user type is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}