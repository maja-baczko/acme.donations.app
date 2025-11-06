<?php

namespace App\Modules\Donation\Http\Resources;

use App\Modules\Campaign\Http\Resources\CampaignResource;
use App\Modules\Donation\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Donation */
class DonationResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'campaign_id' => $this->campaign_id,
            'donor_id' => $this->donor_id,

            'campaign' => new CampaignResource($this->whenLoaded('campaign')),
        ];
    }
}
