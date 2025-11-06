<?php

namespace Database\Factories;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Donation\Models\Donation;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DonationFactory extends Factory {
    protected $model = Donation::class;

    public function definition(): array {
        return [
            'amount' => $this->faker->randomFloat(2, 5, 50000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal', 'bank_transfer']),
            'comment' => $this->faker->optional(0.3)->sentence(),
            'is_anonymous' => $this->faker->boolean(20), // 20% anonymous
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'campaign_id' => Campaign::factory(),
            'donor_id' => User::factory(),
        ];
    }

    // Donation is pending while payment is processed
    public function pending(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    // Donation is completed when payment is approved
    public function completed(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    // Donation is failed if payment has failed
    public function failed(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    // Donor wants to stay anonymous
    public function anonymous(): static {
        return $this->state(fn (array $attributes) => [
            'is_anonymous' => true,
        ]);
    }

    // Small donation : < under 50
    public function small(): static {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, 5, 50),
        ]);
    }

    // Large donation : > over 1000
    public function large(): static {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
        ]);
    }
}
