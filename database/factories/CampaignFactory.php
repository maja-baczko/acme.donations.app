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
            'title' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->text(),
            'goal_amount' => $this->faker->randomFloat(),
            'current_amount' => $this->faker->randomFloat(),
            'status' => $this->faker->word(),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'beneficiary_name' => $this->faker->name(),
            'beneficiary_website' => $this->faker->word(),
            'featured' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'creator_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
