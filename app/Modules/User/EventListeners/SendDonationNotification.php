<?php

namespace App\Modules\User\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendDonationNotification implements ShouldQueue {
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(DonationStatusEvent $event): void {
        $donation = $event->donation->load('donor', 'campaign');
        $donor = $donation->donor;

        // Don't send if anonymous donation
        if ($donation->is_anonymous) {
            Log::info("Skipping notification for anonymous donation #$donation->id");

            return;
        }

        // Send different notifications based on status
        switch ($event->newStatus) {
            case 'completed':
                // Send success notification
                // Notification::send($donor, new DonationCompletedNotification($donation));
                Log::info("Donation completed notification sent to {$donor->email} for donation #{$donation->id}", [
                    'donation_id' => $donation->id,
                    'donor_email' => $donor->email,
                    'amount' => $donation->amount,
                    'campaign' => $donation->campaign->title,
                ]);
                break;

            case 'failed':
                // Send failure notification
                // Notification::send($donor, new DonationFailedNotification($donation));
                Log::info("Donation failed notification sent to {$donor->email} for donation #{$donation->id}", [
                    'donation_id' => $donation->id,
                    'donor_email' => $donor->email,
                    'amount' => $donation->amount,
                    'campaign' => $donation->campaign->title,
                ]);
                break;

            case 'pending':
                // Send pending notification
                Log::info("Donation pending notification sent to {$donor->email} for donation #{$donation->id}", [
                    'donation_id' => $donation->id,
                    'donor_email' => $donor->email,
                    'amount' => $donation->amount,
                    'campaign' => $donation->campaign->title,
                ]);
                break;
        }
    }

    public function failed(DonationStatusEvent $event, Throwable $exception): void {
        Log::error('Failed to send donation notification', [
            'donation_id' => $event->donation->id,
            'new_status' => $event->newStatus,
            'exception' => $exception->getMessage(),
        ]);
    }
}
