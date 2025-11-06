<?php

namespace Database\Factories;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Campaign\Models\Category;
use App\Modules\Donation\Models\Donation;
use App\Modules\Media\Models\Image;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory {
    protected $model = Image::class;

    public function definition(): array {
        $fileName = $this->faker->uuid().'.jpg';

        return [
            'type' => $this->faker->randomElement(['photo', 'profile', 'icon']),
            'entity_type' => $this->faker->randomElement([
                Campaign::class,
                User::class,
                Donation::class,
            ]),
            'entity_id' => $this->faker->numberBetween(1, 100),
            'file_path' => 'storage/images/'.date('Y/m').'/'.$fileName,
            'file_name' => $fileName,
            'is_primary' => $this->faker->boolean(30), // 30% chance of being primary
            'alt_text' => $this->faker->optional(0.7)->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    // Campaign banner
    public function campaign(): static {
        return $this->state(fn (array $attributes) => [
            'type' => 'campaign',
            'entity_type' => Campaign::class,
        ]);
    }

    // User profile image
    public function profile(): static {
        return $this->state(fn (array $attributes) => [
            'type' => 'profile',
            'entity_type' => User::class,
        ]);
    }

    // Category icon
    public function icon(): static {
        return $this->state(fn (array $attributes) => [
            'type' => 'icon',
            'entity_type' => Category::class,
        ]);
    }
}
