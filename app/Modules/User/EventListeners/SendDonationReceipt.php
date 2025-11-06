<?php /** @noinspection ALL */
/** @noinspection ALL */

/** @noinspection ALL */

namespace App\Modules\User\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendDonationReceipt implements ShouldQueue {
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(DonationStatusEvent $event): void {
        // Only send receipt for completed donations
        if ($event->newStatus !== 'completed') {
            return;
        }

        $donation = $event->donation->load('donor', 'campaign', 'payment');
        $donor = $donation->donor;

        // Don't send receipt if anonymous
        if ($donation->is_anonymous) {
            Log::info("Skipping receipt for anonymous donation #{$donation->id}");
            return;
        }

        // Send receipt email
        // Mail::to($donor->email)->send(new DonationReceiptMail($donation));

        Log::info("Donation receipt sent to {$donor->email} for donation #{$donation->id}", [
            'donation_id' => $donation->id,
            'donor_email' => $donor->email,
            'amount' => $donation->amount,
            'campaign' => $donation->campaign->title,
            'date' => $donation->created_at->format('Y-m-d H:i:s'),
            'payment_method' => $donation->payment?->payment_method ?? 'N/A',
        ]);
    }

    public function failed(DonationStatusEvent $event, Throwable $exception): void {
        Log::error('Failed to send donation receipt', [
            'donation_id' => $event->donation->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
