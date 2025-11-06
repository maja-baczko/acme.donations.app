<?php

namespace App\Modules\Campaign\EventListeners;

use App\Modules\Donation\Events\DonationCompletedEvent;

class UpdateCampaignTotal {
    public function __construct() {}

    public function handle(DonationCompletedEvent $event): void {}
}
