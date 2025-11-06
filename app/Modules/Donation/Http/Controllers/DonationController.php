<?php

namespace App\Modules\Donation\Http\Controllers;

use App\Modules\Donation\Http\Requests\CreateDonationRequest;
use App\Modules\Donation\Http\Requests\UpdateDonationRequest;
use App\Modules\Donation\Http\Resources\DonationResource;
use App\Modules\Donation\Models\Donation;
use App\Modules\Donation\Services\DonationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DonationController {
    public function __construct(
        private readonly DonationService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return DonationResource::collection(Donation::all());
    }

    public function create(CreateDonationRequest $request): JsonResponse {
        $donation = $this->service->create($request->validated());

        return (new DonationResource($donation))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Donation $donation): DonationResource {
        return new DonationResource($donation);
    }

    public function update(UpdateDonationRequest $request, Donation $donation): DonationResource {
        $donation = $this->service->update($donation, $request->validated());

        return new DonationResource($donation);
    }

    public function destroy(Donation $donation): JsonResponse {
        try {
            $this->service->delete($donation);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete donation',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
