<?php

namespace Database\Factories;

use App\Modules\Administration\Models\AuditLog;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AuditLogFactory extends Factory {
    protected $model = AuditLog::class;

    public function definition(): array {
        return [
            'action' => $this->faker->word(),
            'entity_type' => $this->faker->word(),
            'entity_id' => $this->faker->randomNumber(),
            'old_value' => $this->faker->words(),
            'new_value' => $this->faker->words(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
