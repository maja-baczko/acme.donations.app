<?php

namespace App\Modules\Campaign\Http\Controllers;

use App\Modules\Campaign\Http\Requests\CreateCampaignRequest;
use App\Modules\Campaign\Http\Requests\UpdateCampaignRequest;
use App\Modules\Campaign\Http\Resources\CampaignResource;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\Campaign\Services\CampaignService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class CampaignController {
    public function __construct(
        private readonly CampaignService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return CampaignResource::collection(Campaign::all());
    }

    /**
     * @throws Throwable
     */
    public function create(CreateCampaignRequest $request): JsonResponse {
        $campaign = $this->service->create($request->validated());

        return (new CampaignResource($campaign))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Campaign $campaign): CampaignResource {
        return new CampaignResource($campaign);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): CampaignResource {
        $campaign = $this->service->update($campaign, $request->validated());

        return new CampaignResource($campaign);
    }

    public function destroy(Campaign $campaign): JsonResponse {
        try {
            $this->service->delete($campaign);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete campaign',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
