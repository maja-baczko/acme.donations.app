<?php

namespace Database\Factories;

use App\Modules\Administration\Models\AuditLog;
use App\Modules\Administration\Models\SystemSetting;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\Donation\Models\Donation;
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
                Campaign::class,
                Donation::class,
                User::class,
                SystemSetting::class,
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

    // Entity was created
    public function created(): static {
        return $this->state(fn (array $attributes) => [
            'action' => 'created',
            'old_value' => json_encode(null),
        ]);
    }

    // Entity was updated
    public function updated(): static {
        return $this->state(fn (array $attributes) => [
            'action' => 'updated',
        ]);
    }

    // Entity was deleted
    public function deleted(): static {
        return $this->state(fn (array $attributes) => [
            'action' => 'deleted',
            'new_value' => json_encode(null),
        ]);
    }

    // Entity was viewed
    public function viewed(): static {
        return $this->state(fn (array $attributes) => [
            'action' => 'viewed',
            'old_value' => json_encode(null),
            'new_value' => json_encode(null),
        ]);
    }

    // Log for Campaign entity
    public function forCampaign(): static {
        return $this->state(fn (array $attributes) => [
            'entity_type' => Campaign::class,
        ]);
    }

    // Log for Donation entity
    public function forDonation(): static {
        return $this->state(fn (array $attributes) => [
            'entity_type' => Donation::class,
        ]);
    }

    // Log for User entity
    public function forUser(): static {
        return $this->state(fn (array $attributes) => [
            'entity_type' => User::class,
        ]);
    }
}
