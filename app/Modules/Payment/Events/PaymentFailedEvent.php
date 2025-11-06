<?php

namespace App\Modules\Payment\Events;

use App\Modules\Payment\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedEvent {
    use Dispatchable, SerializesModels;

    public Payment $payment;
    public string $errorMessage;

    public function __construct(Payment $payment, string $errorMessage) {
        $this->payment = $payment;
        $this->errorMessage = $errorMessage;
    }
}
