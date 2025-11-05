<?php

namespace App\Modules\Campaign\Http\Resources;

use App\Modules\Campaign\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Campaign */
class CampaignResource extends JsonResource {
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'slug' => $this->slug,
			'description' => $this->description,
			'goal_amount' => $this->goal_amount,
			'current_amount' => $this->current_amount,
			'status' => $this->status,
			'start_date' => $this->start_date,
			'end_date' => $this->end_date,
			'beneficiary_name' => $this->beneficiary_name,
			'beneficiary_website' => $this->beneficiary_website,
			'featured' => $this->featured,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,

			'creator_id' => $this->creator_id,
			'category_id' => $this->category_id,

			'category' => new CategoryResource($this->whenLoaded('category')),
		];
	}
}
