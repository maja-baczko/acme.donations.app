<?php

namespace Database\Seeders;

use App\Modules\Campaign\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $categories = [
            [
                'name' => 'Childhood',
                'slug' => 'childhood',
                'is_active' => true,
            ],
            [
                'name' => 'Schooling',
                'slug' => 'schooling',
                'is_active' => true,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'is_active' => true,
            ],
            [
                'name' => 'Ecology',
                'slug' => 'ecology',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
