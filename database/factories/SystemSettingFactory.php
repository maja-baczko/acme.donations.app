<?php

namespace Database\Factories;

use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SystemSettingFactory extends Factory {
    protected $model = SystemSetting::class;

    public function definition(): array {
        return [
            'key' => $this->faker->word(),
            'value' => $this->faker->word(),
            'type' => $this->faker->word(),
            'description' => $this->faker->text(),
            'is_public' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
