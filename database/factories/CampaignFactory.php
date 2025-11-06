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
            'status' => $this->faker->randomElement(['draft', 'active', 'paused', 'completed']),
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

    // Campaign can only been seen by creator and administrators
    public function draft(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'current_amount' => 0,
        ]);
    }

    // Campaign is published and accepting donations
    public function active(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(rand(1, 30)),
        ]);
    }

    // Campaign has reached its goal
    public function completed(): static {
        return $this->state(function (array $attributes) {
            $goal = $attributes['goal_amount'];

            return [
                'status' => 'completed',
                'current_amount' => $goal,
            ];
        });
    }

    // Campaign is put on hold
    public function paused(): static {
        return $this->state(fn (array $attributes) => [
            'status' => 'paused',
        ]);
    }

    // Campaign is featured on homepage
    public function featured(): static {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    // Campaign is ending soon
    public function urgent(): static {
        return $this->state(fn (array $attributes) => [
            'end_date' => Carbon::now()->addDays(rand(1, 7)),
            'status' => 'active',
        ]);
    }
}
