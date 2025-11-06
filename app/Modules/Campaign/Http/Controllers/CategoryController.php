<?php

namespace App\Modules\Campaign\Http\Controllers;

use App\Modules\Campaign\Http\Requests\CreateCategoryRequest;
use App\Modules\Campaign\Http\Requests\UpdateCategoryRequest;
use App\Modules\Campaign\Http\Resources\CategoryResource;
use App\Modules\Campaign\Models\Category;
use App\Modules\Campaign\Services\CategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController {
    public function __construct(
        private readonly CategoryService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return CategoryResource::collection(Category::all());
    }

    public function create(CreateCategoryRequest $request): JsonResponse {
        $category = $this->service->create($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): CategoryResource {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource {
        $category = $this->service->update($category, $request->validated());

        return new CategoryResource($category);
    }

    /**
     * @throws Exception
     */
    public function destroy(Category $category): JsonResponse {
        $this->service->delete($category);

        return response()->json(null, 204);
    }
}
