<?php

namespace App\Modules\User\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;

class SendDonationReceipt {
    public function __construct() {}

    public function handle(DonationStatusEvent $event): void {}
}
