<?php

namespace App\Modules\Payment\Services;

use App\Modules\Donation\Services\DonationService;
use App\Modules\Payment\Events\PaymentCompletedEvent;
use App\Modules\Payment\Events\PaymentFailedEvent;
use App\Modules\Payment\Models\Payment;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class PaymentService {
    protected DonationService $donationService;

    public function __construct(DonationService $donationService) {
        $this->donationService = $donationService;
    }

    /**
     * Create a new payment
     *
     * @param array $data
     * @return Payment
     * @throws Throwable
     */
    public function create(array $data): Payment {
        return DB::transaction(function () use ($data) {
            // Set user_id from auth
            $data['user_id'] = auth()->id();

            // Generate unique transaction reference if not provided
            if (!isset($data['transaction_reference'])) {
                $data['transaction_reference'] = $this->generateTransactionReference();
            }

            // Set default status to processing
            if (!isset($data['status'])) {
                $data['status'] = 'processing';
            }

            // Create payment
            $payment = Payment::create($data);

            return $payment->fresh();
        });
    }

    /**
     * Delete a payment
     *
     * @param Payment $payment
     * @return bool
     * @throws Exception|Throwable
     */
    public function delete(Payment $payment): bool {
        return DB::transaction(function () use ($payment) {
            // Only allow delete if status is not completed
            if ($payment->status === 'completed') {
                throw new Exception('Cannot delete completed payment.');
            }

            return $payment->delete();
        });
    }

    /**
     * Process payment through a gateway
     *
     * @param Payment $payment
     * @param string $gateway
     * @return Payment
     * @throws Throwable
     */
    public function processPayment(Payment $payment, string $gateway): Payment {
        return DB::transaction(function () use ($payment, $gateway) {
            try {
                // Get payment gateway instance (simplified - would use service container)
                // $gatewayInstance = app()->make(PaymentGatewayInterface::class, ['gateway' => $gateway]);

                // Update gateway
                $payment->update(['gateway' => $gateway]);

                // Process payment logic would go here
                // For now, return the payment for further processing

                return $payment->fresh();
            } catch (Exception $e) {
                $this->markAsFailed($payment, $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Mark payment as completed
     *
     * @param Payment $payment
     * @param string $transactionRef
     * @return Payment
     * @throws Throwable
     */
    public function markAsCompleted(Payment $payment, string $transactionRef): Payment {
        return DB::transaction(function () use ($payment, $transactionRef) {
            // Update payment status
            $payment->update([
                'status' => 'completed',
                'transaction_reference' => $transactionRef,
            ]);

            // Dispatch event
            event(new PaymentCompletedEvent($payment));

            // Update related donation if exists
            if ($payment->donation_id) {
                $this->donationService->markAsCompleted($payment->donation);
            }

            return $payment->fresh();
        });
    }

    /**
     * Mark payment as failed
     *
     * @param Payment $payment
     * @param string $errorMessage
     * @return Payment
     * @throws Throwable
     */
    public function markAsFailed(Payment $payment, string $errorMessage): Payment {
        return DB::transaction(function () use ($payment, $errorMessage) {
            // Update payment status
            $payment->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
            ]);

            // Dispatch event
            event(new PaymentFailedEvent($payment, $errorMessage));

            // Update related donation if exists
            if ($payment->donation_id) {
                $this->donationService->markAsFailed($payment->donation, $errorMessage);
            }

            return $payment->fresh();
        });
    }

    /**
     * Get payments by status
     *
     * @param string $status
     * @return Collection
     */
    public function getPaymentsByStatus(string $status): Collection {
        return Payment::where('status', $status)
            ->with(['user', 'donation'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all payments by a user
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserPayments(int $userId): Collection {
        return Payment::where('user_id', $userId)
            ->with('donation.campaign')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Retry a failed payment
     *
     * @param Payment $payment
     * @return Payment
     * @throws Exception|Throwable
     */
    public function retryPayment(Payment $payment): Payment {
        return DB::transaction(function () use ($payment) {
            // Only allow retry for failed payments
            if ($payment->status !== 'failed') {
                throw new Exception('Can only retry failed payments.');
            }

            // Update status to processing
            $payment->update([
                'status' => 'processing',
                'error_message' => null,
            ]);

            return $payment->fresh();
        });
    }

    /**
     * Generate unique transaction reference
     *
     * @return string
     */
    protected function generateTransactionReference(): string {
        return 'TXN-' . strtoupper(Str::random(10)) . '-' . time();
    }
}
