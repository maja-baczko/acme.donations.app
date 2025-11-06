<?php

namespace Database\Seeders;

use App\Modules\Administration\Models\AuditLog;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder {
    // Run the database seeds
    public function run(): void {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Seed users first.');

            return;
        }

        // Create 20 general audit logs
        AuditLog::factory()
            ->count(20)
            ->recycle($users)
            ->create();

        // Create 10 campaign creation logs
        AuditLog::factory()
            ->count(10)
            ->created()
            ->forCampaign()
            ->recycle($users)
            ->create();

        // Create 15 donation update logs
        AuditLog::factory()
            ->count(15)
            ->updated()
            ->forDonation()
            ->recycle($users)
            ->create();

        // Create 5 user view logs
        AuditLog::factory()
            ->count(5)
            ->viewed()
            ->forUser()
            ->recycle($users)
            ->create();
    }
}
