<?php

namespace App\Modules\User\EventListeners;

use App\Modules\Donation\EventListeners\DonationStatusEvent;

class SendDonationNotification {
    public function __construct() {}

    // completed or failed
    public function handle(DonationStatusEvent $event): void {}
}
