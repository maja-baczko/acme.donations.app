<?php

namespace App\Modules\Donation\Events;

use App\Modules\Donation\Models\Donation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationStatusEvent {
    use Dispatchable, SerializesModels;

    public Donation $donation;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(Donation $donation, string $oldStatus, string $newStatus) {
        $this->donation = $donation;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
