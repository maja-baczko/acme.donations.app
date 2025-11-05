<?php

namespace App\Modules\Media\Http\Controllers;

use App\Modules\Media\Http\Resources\ImageResource;
use App\Modules\Media\Models\Image;
use Illuminate\Http\Request;

class ImageController {
    public function index() {
        return ImageResource::collection(Image::all());
    }

    public function create(Request $request) {
        $data = $request->validate([
            'type' => ['required'],
            'entity_type' => ['required'],
            'entity_id' => ['required'],
            'file_path' => ['required'],
            'file_name' => ['required'],
            'is_primary' => ['boolean'],
            'alt_text' => ['required'],
        ]);

        return new ImageResource(Image::create($data));
    }

    public function show(Image $image) {
        return new ImageResource($image);
    }

    public function update(Request $request, Image $image) {
        $data = $request->validate([
            'type' => ['required'],
            'entity_type' => ['required'],
            'entity_id' => ['required'],
            'file_path' => ['required'],
            'file_name' => ['required'],
            'is_primary' => ['boolean'],
            'alt_text' => ['required'],
        ]);

        $image->update($data);

        return new ImageResource($image);
    }

    public function destroy(Image $image) {
        $image->delete();

        return response()->json();
    }
}
