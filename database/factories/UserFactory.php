<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserType;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'phone' => $this->faker->phoneNumber(),
            'phone_verified_at' => $this->faker->boolean(70) ? now() : null,
            'avatar' => null,
            'is_active' => $this->faker->boolean(90),
            'last_login_at' => $this->faker->dateTimeBetween('-1 month'),
            'last_login_ip' => $this->faker->ipv4(),
            'timezone' => $this->faker->timezone(),
            'language' => $this->faker->randomElement(['en', 'ar', 'fr', 'es']),
            'user_type_id' => UserType::factory(),
            'profile_data' => [
                'bio' => $this->faker->sentence(),
                'website' => $this->faker->url(),
                'location' => $this->faker->city(),
            ],
            'settings' => [
                'theme' => $this->faker->randomElement(['light', 'dark']),
                'notifications' => $this->faker->boolean(80),
                'email_notifications' => $this->faker->boolean(70),
            ],
            'preferences' => [
                'display_name_format' => $this->faker->randomElement(['name', 'username', 'email']),
                'date_format' => $this->faker->randomElement(['Y-m-d', 'd/m/Y', 'm/d/Y']),
                'time_format' => $this->faker->randomElement(['24h', '12h']),
            ],
            'two_factor_confirmed_at' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user is active.
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
     * Indicate that the user is inactive.
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

    /**
     * Indicate that the user has two-factor authentication enabled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTwoFactor()
    {
        return $this->state(function (array $attributes) {
            return [
                'two_factor_secret' => 'test-secret',
                'two_factor_recovery_codes' => ['recovery-code-1', 'recovery-code-2'],
                'two_factor_confirmed_at' => true,
            ];
        });
    }
}