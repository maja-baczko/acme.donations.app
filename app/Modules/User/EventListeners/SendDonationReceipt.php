<?php

namespace App\Modules\User\EventListeners;

use App\Modules\Donation\Events\DonationCompletedEvent;

class SendDonationReceipt {
    public function __construct() {}

    public function handle(DonationCompletedEvent $event): void {}
}
