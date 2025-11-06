<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    // Run the database seeds
    public function run(): void {
        // Create 3 admin users
        User::factory()
            ->count(3)
            ->admin()
            ->create();

        // Create 5 manager users
        User::factory()
            ->count(5)
            ->manager()
            ->create();

        // Create 15 employee users
        User::factory()
            ->count(15)
            ->employee()
            ->create();

        // Create 3 unverified employees
        User::factory()
            ->count(3)
            ->employee()
            ->unverified()
            ->create();

        // Create 2 inactive employees
        User::factory()
            ->count(2)
            ->employee()
            ->inactive()
            ->create();
    }
}
