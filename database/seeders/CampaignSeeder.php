<?php

namespace Database\Seeders;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder {
    // Run the database seeds
    public function run(): void {
        $creators = User::role(['admin', 'manager'])->get();

        if ($creators->isEmpty()) {
            $this->command->warn('No admins or managers found. Create users first.');

            return;
        }

        // Create 5 active campaigns
        Campaign::factory()
            ->count(5)
            ->active()
            ->recycle($creators)
            ->create();

        // Create 3 featured active campaigns
        Campaign::factory()
            ->count(3)
            ->active()
            ->featured()
            ->recycle($creators)
            ->create();

        // Create 2 urgent campaigns
        Campaign::factory()
            ->count(2)
            ->active()
            ->urgent()
            ->recycle($creators)
            ->create();

        // Create 2 completed campaigns
        Campaign::factory()
            ->count(2)
            ->completed()
            ->recycle($creators)
            ->create();

        // Create 3 draft campaigns
        Campaign::factory()
            ->count(3)
            ->draft()
            ->recycle($creators)
            ->create();

        // Create 2 paused campaigns
        Campaign::factory()
            ->count(2)
            ->paused()
            ->recycle($creators)
            ->create();
    }
}
