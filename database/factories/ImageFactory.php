<?php

namespace Database\Factories;

use App\Modules\Media\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory {
    protected $model = Image::class;

    public function definition(): array {
        $fileName = $this->faker->uuid().'.jpg';

        return [
            'type' => $this->faker->randomElement(['campaign', 'profile', 'receipt']),
            'entity_type' => $this->faker->randomElement([
                'App\Modules\Campaign\Models\Campaign',
                'App\Modules\User\Models\User',
                'App\Modules\Donation\Models\Donation',
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
}
