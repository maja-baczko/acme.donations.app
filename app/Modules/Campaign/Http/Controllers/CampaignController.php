<?php

namespace App\Modules\Campaign\Http\Controllers;

use App\Modules\Campaign\Http\Resources\CampaignResource;
use App\Modules\Campaign\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController {
    public function index() {
        return CampaignResource::collection(Campaign::all());
    }

    public function create(Request $request) {
        $data = $request->validate([
            'creator_id' => ['required', 'exists:users'],
            'category_id' => ['required', 'exists:categories'],
            'title' => ['required'],
            'slug' => ['required'],
            'description' => ['required'],
            'goal_amount' => ['required', 'numeric'],
            'current_amount' => ['required', 'numeric'],
            'status' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'beneficiary_name' => ['required'],
            'beneficiary_website' => ['required'],
            'featured' => ['boolean'],
        ]);

        return new CampaignResource(Campaign::create($data));
    }

    public function show(Campaign $campaign) {
        return new CampaignResource($campaign);
    }

    public function update(Request $request, Campaign $campaign) {
        $data = $request->validate([
            'creator_id' => ['required', 'exists:users'],
            'category_id' => ['required', 'exists:categories'],
            'title' => ['required'],
            'slug' => ['required'],
            'description' => ['required'],
            'goal_amount' => ['required', 'numeric'],
            'current_amount' => ['required', 'numeric'],
            'status' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'beneficiary_name' => ['required'],
            'beneficiary_website' => ['required'],
            'featured' => ['boolean'],
        ]);

        $campaign->update($data);

        return new CampaignResource($campaign);
    }

    public function destroy(Campaign $campaign) {
        $campaign->delete();

        return response()->json();
    }
}
