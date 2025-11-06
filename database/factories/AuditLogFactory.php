<?php

namespace Database\Factories;

use App\Modules\Administration\Models\AuditLog;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AuditLogFactory extends Factory {
    protected $model = AuditLog::class;

    public function definition(): array {
        $oldValue = ['status' => 'pending', 'amount' => 100.00];
        $newValue = ['status' => 'completed', 'amount' => 100.00];

        return [
            'action' => $this->faker->randomElement(['created', 'updated', 'deleted', 'viewed']),
            'entity_type' => $this->faker->randomElement([
                'App\Modules\Campaign\Models\Campaign',
                'App\Modules\Donation\Models\Donation',
                'App\Modules\User\Models\User',
                'App\Modules\Administration\Models\SystemSetting',
            ]),
            'entity_id' => $this->faker->numberBetween(1, 1000),
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($newValue),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
