<?php

namespace App\Modules\Campaign\Events;

use App\Modules\Campaign\Models\Campaign;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignGoalReachedEvent {
    use Dispatchable, SerializesModels;

    public Campaign $campaign;

    public function __construct(Campaign $campaign) {
        $this->campaign = $campaign;
    }
}
