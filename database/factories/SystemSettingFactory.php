<?php

namespace Database\Factories;

use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SystemSettingFactory extends Factory {
    protected $model = SystemSetting::class;

    public function definition(): array {
        $type = $this->faker->randomElement(['string', 'integer', 'boolean', 'text', 'json']);

        // Generate appropriate value based on type
        $value = match ($type) {
            'string' => $this->faker->word(),
            'integer' => (string) $this->faker->numberBetween(1, 100),
            'boolean' => $this->faker->boolean() ? 'true' : 'false',
            'text' => $this->faker->paragraph(),
            'json' => json_encode(['key' => $this->faker->word()]),
        };

        return [
            'key' => $this->faker->unique()->slug(2),
            'value' => $value,
            'type' => $type,
            'description' => $this->faker->sentence(),
            'is_public' => $this->faker->boolean(60), // 60% public
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
