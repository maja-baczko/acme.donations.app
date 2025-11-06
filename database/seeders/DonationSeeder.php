<?php

namespace Database\Seeders;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Donation\Models\Donation;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder {
    // Run the database seeds
    public function run(): void {
        $campaigns = Campaign::whereIn('status', ['active', 'completed'])->get();
        $users = User::all();

        if ($campaigns->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No campaigns or users found. Seed them first.');

            return;
        }

        // Create 30 completed donations
        Donation::factory()
            ->count(30)
            ->completed()
            ->recycle($campaigns)
            ->recycle($users)
            ->create();

        // Create 5 pending donations
        Donation::factory()
            ->count(5)
            ->pending()
            ->recycle($campaigns)
            ->recycle($users)
            ->create();

        // Create 2 failed donations
        Donation::factory()
            ->count(2)
            ->failed()
            ->recycle($campaigns)
            ->recycle($users)
            ->create();

        // Create 5 anonymous donations
        Donation::factory()
            ->count(5)
            ->completed()
            ->anonymous()
            ->recycle($campaigns)
            ->recycle($users)
            ->create();

        // Create 3 small donations
        Donation::factory()
            ->count(3)
            ->completed()
            ->small()
            ->recycle($campaigns)
            ->recycle($users)
            ->create();

        // Create 2 large donations
        Donation::factory()
            ->count(2)
            ->completed()
            ->large()
            ->recycle($campaigns)
            ->recycle($users)
            ->create();
    }
}
