<?php

namespace Database\Factories;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Campaign\Models\Category;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CampaignFactory extends Factory {
    protected $model = Campaign::class;

    public function definition(): array {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraphs(3, true),
            'goal_amount' => $this->faker->randomFloat(2, 1000, 100000),
            'current_amount' => $this->faker->randomFloat(2, 0, 50000),
            'status' => $this->faker->randomElement(['draft', 'active', 'completed', 'cancelled']),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'beneficiary_name' => $this->faker->company(),
            'beneficiary_website' => $this->faker->url(),
            'featured' => $this->faker->boolean(20), // 20% chance of being featured
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'creator_id' => User::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::first()?->id,
        ];
    }
}
