<?php

namespace Database\Factories;

use App\Modules\Donation\Models\Donation;
use App\Modules\Payment\Models\Payment;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentFactory extends Factory {
    protected $model = Payment::class;

    public function definition(): array {
        return [
            'amount' => $this->faker->randomFloat(2, 5, 50000),
            'status' => $this->faker->randomElement(['processing', 'completed', 'failed']),
            'gateway' => $this->faker->randomElement(['stripe', 'paypal', 'bank_transfer']),
            'transaction_reference' => $this->faker->unique()->uuid(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'donation_id' => Donation::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function processing(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    public function completed(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function failed(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
