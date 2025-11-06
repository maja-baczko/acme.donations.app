<?php /** @noinspection ALL */
/** @noinspection ALL */

/** @noinspection ALL */

namespace App\Modules\Campaign\Services;

use App\Modules\Campaign\Events\CampaignGoalReachedEvent;
use App\Modules\Campaign\Models\Campaign;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CampaignService {
    /**
     * Create a new campaign
     *
     * @param array $data
     * @return Campaign
     * @throws Throwable
     */
    public function create(array $data): Campaign {
        return DB::transaction(function () use ($data) {
            // Set creator_id from auth
            $data['creator_id'] = auth()->id();

            // Initialize current_amount to 0
            $data['current_amount'] = 0;

            // Create campaign
            $campaign = Campaign::create($data);

            return $campaign->fresh();
        });
    }

    /**
     * Update an existing campaign
     *
     * @param Campaign $campaign
     * @param array $data
     * @return Campaign
     * @throws Throwable
     * @throws Throwable
     */
    public function update(Campaign $campaign, array $data): Campaign {
        return DB::transaction(function () use ($campaign, $data) {
            // Validate status transitions if status is being changed
            if (isset($data['status']) && $data['status'] !== $campaign->status) {
                $this->validateStatusTransition($campaign->status, $data['status']);
            }

            // Update campaign
            $campaign->update($data);

            return $campaign->fresh();
        });
    }

    /**
     * Delete a campaign
     *
     * @param Campaign $campaign
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function delete(Campaign $campaign): bool {
        return DB::transaction(function () use ($campaign) {
            // Check if has completed donations
            $hasCompletedDonations = $campaign->donations()
                ->where('status', 'completed')
                ->exists();

            if ($hasCompletedDonations) {
                throw new Exception('Cannot delete campaign with completed donations.');
            }

            return $campaign->delete();
        });
    }

    /**
     * Update campaign total amount from completed donations
     *
     * @param Campaign $campaign
     * @return Campaign
     * @throws Throwable
     * @throws Throwable
     */
    public function updateTotalAmount(Campaign $campaign): Campaign {
        return DB::transaction(function () use ($campaign) {
            $totalAmount = $campaign->donations()
                ->where('status', 'completed')
                ->sum('amount');

            $campaign->update(['current_amount' => $totalAmount]);

            return $campaign->fresh();
        });
    }

    /**
     * Check if campaign goal is reached and dispatch event
     *
     * @param Campaign $campaign
     * @return bool
     */
    public function checkGoalReached(Campaign $campaign): bool {
        $goalReached = $campaign->current_amount >= $campaign->goal_amount;

        if ($goalReached) {
            event(new CampaignGoalReachedEvent($campaign));
        }

        return $goalReached;
    }

    /**
     * Get active campaigns
     *
     * @return Collection
     */
    public function getActiveCampaigns(): Collection {
        return Campaign::where('status', 'active')->get();
    }

    /**
     * Get featured and active campaigns
     *
     * @return Collection
     */
    public function getFeaturedCampaigns(): Collection {
        return Campaign::where('featured', true)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Get campaigns ending soon (within X days)
     *
     * @param int $days
     * @return Collection
     */
    public function getEndingSoon(int $days = 7): Collection {
        $endDate = Carbon::now()->addDays($days);

        return Campaign::where('status', 'active')
            ->where('end_date', '<=', $endDate)
            ->where('end_date', '>=', Carbon::now())
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Transition campaign status with validation
     *
     * @param Campaign $campaign
     * @param string $newStatus
     * @return Campaign
     * @throws Exception
     * @throws Throwable
     */
    public function transitionStatus(Campaign $campaign, string $newStatus): Campaign {
        return DB::transaction(function () use ($campaign, $newStatus) {
            $this->validateStatusTransition($campaign->status, $newStatus);

            $campaign->update(['status' => $newStatus]);

            return $campaign->fresh();
        });
    }

    /**
     * Validate status transition
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @throws Exception
     */
    protected function validateStatusTransition(string $currentStatus, string $newStatus): void {
        $validTransitions = [
            'draft' => ['active', 'cancelled'],
            'active' => ['completed', 'cancelled', 'suspended'],
            'suspended' => ['active', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (!isset($validTransitions[$currentStatus])) {
            throw new Exception("Invalid current status: {$currentStatus}");
        }

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            throw new Exception("Cannot transition from {$currentStatus} to {$newStatus}");
        }
    }
}
