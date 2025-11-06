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
            'amount' => $this->faker->word(),
            'status' => $this->faker->word(),
            'gateway' => $this->faker->word(),
            'transaction_reference' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'donation_id' => Donation::factory(),
            'user_id' => User::factory(),
        ];
    }
}
