<?php

namespace Tests;

use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
    protected function setUp(): void {
        parent::setUp();

        // Seed permissions for all tests
        $this->seed(PermissionSeeder::class);
    }
}
