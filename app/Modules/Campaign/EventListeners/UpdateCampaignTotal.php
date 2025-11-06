<?php

namespace App\Modules\Campaign\EventListeners;

use App\Modules\Campaign\Events\CampaignGoalReachedEvent;
use App\Modules\Donation\Events\DonationStatusEvent;
use App\Modules\Donation\Models\Donation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateCampaignTotal implements ShouldQueue {
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(DonationStatusEvent $event): void {
        // Only update if new status is 'completed'
        if ($event->newStatus !== 'completed') {
            return;
        }

        $campaign = $event->donation->campaign;

        // Sum all completed donations for this campaign
        $total = Donation::where('campaign_id', $campaign->id)
            ->where('status', 'completed')
            ->sum('amount');

        $campaign->update(['current_amount' => $total]);

        Log::info("Campaign total updated for campaign #".$campaign->id, [
            'campaign_id' => $campaign->id,
            'previous_amount' => $campaign->current_amount,
            'new_amount' => $total,
            'donation_id' => $event->donation->id,
        ]);

        // Check if goal is reached
        if ($campaign->current_amount >= $campaign->goal_amount) {
            event(new CampaignGoalReachedEvent($campaign));

            Log::info("Campaign goal reached for campaign #".$campaign->id, [
                'campaign_id' => $campaign->id,
                'goal_amount' => $campaign->goal_amount,
                'current_amount' => $campaign->current_amount,
            ]);
        }
    }

    public function failed(DonationStatusEvent $event, Throwable $exception): void {
        Log::error('Failed to update campaign total', [
            'donation_id' => $event->donation->id,
            'campaign_id' => $event->donation->campaign_id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
