<?php

namespace App\Modules\User\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;

class SendDonationNotification {
    public function __construct() {}

    public function handleCompleted(DonationStatusEvent $event): void {
        // Send notification for completed donation
    }

    public function handleFailed(DonationStatusEvent $event): void {
        // Send notification for failed donation
    }
}
