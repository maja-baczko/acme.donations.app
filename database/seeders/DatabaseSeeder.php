<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed permissions and roles first
        $this->call([
            PermissionSeeder::class,
        ]);

        // Seed system settings and categories
        $this->call([
            SystemSettingSeeder::class,
            CategorySeeder::class,
        ]);

        // Create test user with admin role
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'department' => 'IT',
                'function' => 'Administrator',
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create test manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'firstname' => 'Manager',
                'lastname' => 'User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'department' => 'Operations',
                'function' => 'Manager',
            ]
        );
        if (!$manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }

        // Create test employee user
        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'firstname' => 'Employee',
                'lastname' => 'User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'department' => 'Finance',
                'function' => 'Analyst',
            ]
        );
        if (!$employee->hasRole('employee')) {
            $employee->assignRole('employee');
        }
    }
}
