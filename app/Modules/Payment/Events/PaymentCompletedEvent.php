<?php

namespace App\Modules\Payment\Events;

use App\Modules\Payment\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCompletedEvent {
    use Dispatchable, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }
}
