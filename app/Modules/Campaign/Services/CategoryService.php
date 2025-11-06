<?php

namespace App\Modules\Campaign\Services;

use App\Modules\Campaign\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService {
    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     * @throws \Throwable
     * @throws \Throwable
     */
    public function create(array $data): Category {
        return DB::transaction(function () use ($data) {
            $category = Category::create($data);
            return $category->fresh();
        });
    }

    /**
     * Update an existing category
     *
     * @param Category $category
     * @param array $data
     * @return Category
     * @throws \Throwable
     * @throws \Throwable
     */
    public function update(Category $category, array $data): Category {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            return $category->fresh();
        });
    }

    /**
     * Delete a category
     *
     * @param Category $category
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function delete(Category $category): bool {
        return DB::transaction(function () use ($category) {
            // Check if has campaigns
            $hasCampaigns = $category->campaigns()->exists();

            if ($hasCampaigns) {
                throw new Exception('Cannot delete category with associated campaigns.');
            }

            return $category->delete();
        });
    }

    /**
     * Get active categories (is_active = true)
     *
     * @return Collection
     */
    public function getActiveCategories(): Collection {
        return Category::where('is_active', true)->get();
    }

    /**
     * Get categories with campaigns count
     *
     * @return Collection
     */
    public function getCategoriesWithCampaignCount(): Collection {
        return Category::withCount('campaigns')->get();
    }

    /**
     * Toggle category active status
     *
     * @param Category $category
     * @return Category
     * @throws \Throwable
     * @throws \Throwable
     */
    public function toggleActive(Category $category): Category {
        return DB::transaction(function () use ($category) {
            $category->update(['is_active' => !$category->is_active]);
            return $category->fresh();
        });
    }
}
