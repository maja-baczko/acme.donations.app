<?php

namespace App\Modules\Media\Http\Controllers;

use App\Modules\Media\Http\Requests\CreateImageRequest;
use App\Modules\Media\Http\Requests\UpdateImageRequest;
use App\Modules\Media\Http\Resources\ImageResource;
use App\Modules\Media\Models\Image;
use App\Modules\Media\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ImageController {
    public function __construct(
        private readonly ImageService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return ImageResource::collection(Image::all());
    }

    public function create(CreateImageRequest $request): JsonResponse {
        $image = $this->service->create($request->validated());

        return (new ImageResource($image))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Image $image): ImageResource {
        return new ImageResource($image);
    }

    public function update(UpdateImageRequest $request, Image $image): ImageResource {
        $image = $this->service->update($image, $request->validated());

        return new ImageResource($image);
    }

    public function destroy(Image $image): JsonResponse {
        $this->service->delete($image);

        return response()->json(null, 204);
    }
}
