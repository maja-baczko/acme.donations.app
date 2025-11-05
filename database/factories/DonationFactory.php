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
            'amount' => $this->faker->randomFloat(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'campaign_id' => Campaign::factory(),
            'donor_id' => User::factory(),
        ];
    }
}
