<?php

namespace App\Modules\Campaign\EventListeners;

use App\Modules\Donation\Events\DonationStatusEvent;

class UpdateCampaignTotal {
    public function __construct() {}

    public function handle(DonationStatusEvent $event): void {}
}
