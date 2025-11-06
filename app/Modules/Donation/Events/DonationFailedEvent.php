<?php

namespace App\Modules\Donation\Events;

use Illuminate\Foundation\Events\Dispatchable;

class DonationFailedEvent {
    use Dispatchable;

    public function __construct() {}
}
