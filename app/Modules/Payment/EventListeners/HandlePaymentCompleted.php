<?php

namespace App\Modules\Payment\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;
use App\Modules\Payment\Events\PaymentCompletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class HandlePaymentCompleted implements ShouldQueue {
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(PaymentCompletedEvent $event): void {
        $payment = $event->payment->load('donation');
        $donation = $payment->donation;

        // Update donation status to completed
        if ($donation && $donation->status !== 'completed') {
            $oldStatus = $donation->status;
            $donation->update(['status' => 'completed']);

            // Dispatch donation status event
            event(new DonationStatusEvent($donation, $oldStatus, 'completed'));

            Log::info("Payment completed - updated donation #{$donation->id} to completed", [
                'payment_id' => $payment->id,
                'donation_id' => $donation->id,
                'amount' => $payment->amount,
                'old_status' => $oldStatus,
                'new_status' => 'completed',
            ]);
        } else {
            Log::info("Payment completed but donation already completed or not found", [
                'payment_id' => $payment->id,
                'donation_id' => $donation?->id,
                'donation_status' => $donation?->status,
            ]);
        }
    }

    public function failed(PaymentCompletedEvent $event, Throwable $exception): void {
        Log::error('Failed to handle payment completion', [
            'payment_id' => $event->payment->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
