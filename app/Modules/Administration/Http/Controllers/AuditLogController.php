<?php

namespace App\Modules\Administration\Http\Controllers;

use App\Modules\Administration\Http\Requests\CreateAuditLogRequest;
use App\Modules\Administration\Http\Resources\AuditLogResource;
use App\Modules\Administration\Models\AuditLog;
use App\Modules\Administration\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

readonly class AuditLogController {
    public function __construct(
        private AuditLogService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return AuditLogResource::collection(AuditLog::all());
    }

    /**
     * @throws Throwable
     */
    public function create(CreateAuditLogRequest $request): JsonResponse {
        $log = $this->service->create($request->validated());

        return (new AuditLogResource($log))
            ->response()
            ->setStatusCode(201);
    }

    public function show(AuditLog $auditLog): AuditLogResource {
        return new AuditLogResource($auditLog);
    }
}
