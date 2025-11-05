<?php

namespace Database\Factories;

use App\Modules\Media\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory {
    protected $model = Image::class;

    public function definition(): array {
        return [
            'type' => $this->faker->word(),
            'entity_type' => $this->faker->word(),
            'entity_id' => $this->faker->word(),
            'file_path' => $this->faker->word(),
            'file_name' => $this->faker->name(),
            'is_primary' => $this->faker->boolean(),
            'alt_text' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
