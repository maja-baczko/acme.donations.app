<?php

namespace Database\Factories;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory {
    protected $model = User::class;

    // Password being used by the factory
    protected static ?string $password = null;

    public function definition(): array {
        return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Marketing', 'Operations']),
            'function' => $this->faker->randomElement(['Developer', 'Manager', 'Analyst', 'Coordinator', 'Director']),
            'still_working' => $this->faker->boolean(80),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    // user's email address is unverified
    public function unverified(): static {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // user no longer works for the company
    public function inactive(): static {
        return $this->state(fn (array $attributes) => [
            'still_working' => false,
        ]);
    }

    public function admin(): static {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function manager(): static {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('manager');
        });
    }

    public function employee(): static {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('employee');
        });
    }
}
