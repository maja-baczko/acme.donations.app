<?php

namespace App\Modules\Campaign\Http\Controllers;

use App\Modules\Campaign\Http\Resources\CategoryResource;
use App\Modules\Campaign\Models\Category;
use Illuminate\Http\Request;

class CategoryController {
	public function index() {
		return CategoryResource::collection(Category::all());
	}

	public function create(Request $request) {
		$data = $request->validate([
			'name' => ['required'],
			'slug' => ['required'],
			'icon' => ['required', 'exists:images'],
			'is_active' => ['boolean'],
		]);

		return new CategoryResource(Category::create($data));
	}

	public function show(Category $category) {
		return new CategoryResource($category);
	}

	public function update(Request $request, Category $category) {
		$data = $request->validate([
			'name' => ['required'],
			'slug' => ['required'],
			'icon' => ['required', 'exists:images'],
			'is_active' => ['boolean'],
		]);

		$category->update($data);

		return new CategoryResource($category);
	}

	public function destroy(Category $category) {
		$category->delete();

		return response()->json();
	}
}
