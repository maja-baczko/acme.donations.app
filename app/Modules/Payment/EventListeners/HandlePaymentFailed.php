<?php

namespace App\Modules\Payment\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;
use App\Modules\Payment\Events\PaymentFailedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class HandlePaymentFailed implements ShouldQueue {
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(PaymentFailedEvent $event): void {
        $payment = $event->payment->load('donation');
        $donation = $payment->donation;

        // Update donation status to failed
        if ($donation && $donation->status !== 'failed') {
            $oldStatus = $donation->status;
            $donation->update(['status' => 'failed']);

            // Dispatch donation status event
            event(new DonationStatusEvent($donation, $oldStatus, 'failed'));

            Log::error("Payment failed - updated donation #{$donation->id} to failed", [
                'payment_id' => $payment->id,
                'donation_id' => $donation->id,
                'amount' => $payment->amount,
                'old_status' => $oldStatus,
                'new_status' => 'failed',
                'error' => $event->errorMessage,
            ]);
        } else {
            Log::warning('Payment failed but donation already failed or not found', [
                'payment_id' => $payment->id,
                'donation_id' => $donation?->id,
                'donation_status' => $donation?->status,
                'error' => $event->errorMessage,
            ]);
        }
    }

    public function failed(PaymentFailedEvent $event, Throwable $exception): void {
        Log::error('Failed to handle payment failure', [
            'payment_id' => $event->payment->id,
            'error_message' => $event->errorMessage,
            'exception' => $exception->getMessage(),
        ]);
    }
}
