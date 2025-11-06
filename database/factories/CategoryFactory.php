<?php

namespace Database\Factories;

use App\Modules\Campaign\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory {
    protected $model = Category::class;

    public function definition(): array {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(),
            'icon' => null, // Optional: can be set if needed in specific tests
            'is_active' => true,
        ];
    }

    public function inactive(): static {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
