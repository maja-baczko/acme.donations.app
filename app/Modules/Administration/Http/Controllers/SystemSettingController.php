<?php

namespace App\Modules\Administration\Http\Controllers;

use App\Modules\Administration\Http\Requests\CreateSystemSettingRequest;
use App\Modules\Administration\Http\Requests\UpdateSystemSettingRequest;
use App\Modules\Administration\Http\Resources\SystemSettingResource;
use App\Modules\Administration\Models\SystemSetting;
use App\Modules\Administration\Services\SystemSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class SystemSettingController {
    public function __construct(
        private readonly SystemSettingService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        $settings = $this->service->getAllSettings();
        return SystemSettingResource::collection($settings);
    }

    public function public(): AnonymousResourceCollection {
        $settings = $this->service->getPublicSettings();
        return SystemSettingResource::collection($settings);
    }

    /**
     * @throws Throwable
     */
    public function create(CreateSystemSettingRequest $request): JsonResponse {
        $setting = $this->service->create($request->validated());

        return (new SystemSettingResource($setting))
            ->response()
            ->setStatusCode(201);
    }

    public function show(SystemSetting $systemSetting): SystemSettingResource {
        return new SystemSettingResource($systemSetting);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateSystemSettingRequest $request, SystemSetting $systemSetting): SystemSettingResource {
        $setting = $this->service->update($systemSetting, $request->validated());

        return new SystemSettingResource($setting);
    }

    /**
     * @throws Throwable
     */
    public function destroy(SystemSetting $systemSetting): JsonResponse {
        $this->service->delete($systemSetting);

        return response()->json(null, 204);
    }
}
