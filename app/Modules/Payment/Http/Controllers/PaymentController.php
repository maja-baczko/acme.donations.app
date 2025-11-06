<?php

namespace App\Modules\Payment\Http\Controllers;

use App\Modules\Payment\Http\Requests\CreatePaymentRequest;
use App\Modules\Payment\Http\Resources\PaymentResource;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class PaymentController {
    public function __construct(
        private readonly PaymentService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return PaymentResource::collection(Payment::all());
    }

    /**
     * @throws Throwable
     */
    public function create(CreatePaymentRequest $request): JsonResponse {
        $payment = $this->service->create($request->validated());

        return (new PaymentResource($payment))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Payment $payment): PaymentResource {
        return new PaymentResource($payment);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Payment $payment): JsonResponse {
        $this->service->delete($payment);

        return response()->json(null, 204);
    }
}
