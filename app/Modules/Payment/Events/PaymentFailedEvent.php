<?php

namespace App\Modules\Payment\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PaymentFailedEvent {
    use Dispatchable;

    public function __construct() {}
}
