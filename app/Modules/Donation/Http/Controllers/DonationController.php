<?php

namespace App\Modules\Donation\Http\Controllers;

use App\Modules\Donation\Http\Resources\DonationResource;
use App\Modules\Donation\Models\Donation;
use Illuminate\Http\Request;

class DonationController {
    public function index() {
        return DonationResource::collection(Donation::all());
    }

    public function create(Request $request) {
        $data = $request->validate([
            'campaign_id' => ['required', 'exists:campaigns'],
            'donor_id' => ['required', 'exists:users'],
            'amount' => ['required', 'numeric'],
            'status' => ['required'],
        ]);

        return new DonationResource(Donation::create($data));
    }

    public function show(Donation $donation) {
        return new DonationResource($donation);
    }

    public function update(Request $request, Donation $donation) {
        $data = $request->validate([
            'campaign_id' => ['required', 'exists:campaigns'],
            'donor_id' => ['required', 'exists:users'],
            'amount' => ['required', 'numeric'],
            'status' => ['required'],
        ]);

        $donation->update($data);

        return new DonationResource($donation);
    }

    public function destroy(Donation $donation) {
        $donation->delete();

        return response()->json();
    }
}
