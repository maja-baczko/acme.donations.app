<?php

namespace App\Modules\Payment\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PaymentCompletedEvent {
    use Dispatchable;

    public function __construct() {}
}
