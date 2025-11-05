<?php

namespace App\Modules\Media\Http\Resources;

use App\Modules\Media\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Image */
class ImageResource extends JsonResource {
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'type' => $this->type,
			'entity_type' => $this->entity_type,
			'entity_id' => $this->entity_id,
			'file_path' => $this->file_path,
			'file_name' => $this->file_name,
			'is_primary' => $this->is_primary,
			'alt_text' => $this->alt_text,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
