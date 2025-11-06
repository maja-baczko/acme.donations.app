<?php

namespace App\Modules\Payment\Http\Controllers;

use App\Modules\Payment\Http\Requests\CreatePaymentRequest;
use App\Modules\Payment\Http\Resources\PaymentResource;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
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
    public function store(CreatePaymentRequest $request): JsonResponse {
        $payment = $this->service->create($request->validated());

        return (new PaymentResource($payment))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Payment $payment): PaymentResource {
        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment): JsonResponse {
        try {
            $this->service->delete($payment);

            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Generate and return payment receipt
     *
     * @param Payment $payment
     * @return JsonResponse
     */
    public function receipt(Payment $payment): JsonResponse {
        // Authorization check
        if (! Gate::allows('viewReceipt', $payment)) {
            return response()->json([
                'message' => 'You are not authorized to view this receipt.',
            ], 403);
        }

        try {
            $receiptData = $this->service->generateReceipt($payment);

            return response()->json([
                'success' => true,
                'receipt' => $receiptData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
