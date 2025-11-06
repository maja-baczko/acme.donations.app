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
}
