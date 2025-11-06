<?php

namespace App\Modules\Donation\Services;

use App\Modules\Campaign\Services\CampaignService;
use App\Modules\Donation\Events\DonationStatusEvent;
use App\Modules\Donation\Models\Donation;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class DonationService {
    protected CampaignService $campaignService;

    public function __construct(CampaignService $campaignService) {
        $this->campaignService = $campaignService;
    }

    /**
     * Create a new donation
     *
     * @param array $data
     * @return Donation
     * @throws Throwable
     * @throws Throwable
     */
    public function create(array $data): Donation {
        return DB::transaction(function () use ($data) {
            // Set donor_id from auth
            $data['donor_id'] = auth()->id();

            // Set initial status to pending
            $data['status'] = 'pending';

            // Create donation
            $donation = Donation::create($data);

            return $donation->fresh();
        });
    }

    /**
     * Update an existing donation
     *
     * @param Donation $donation
     * @param array $data
     * @return Donation
     * @throws Throwable
     * @throws Throwable
     */
    public function update(Donation $donation, array $data): Donation {
        return DB::transaction(function () use ($donation, $data) {
            $oldStatus = $donation->status;

            // Update donation
            $donation->update($data);

            // Dispatch event if status changed
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                event(new DonationStatusEvent($donation, $oldStatus, $data['status']));
            }

            return $donation->fresh();
        });
    }

    /**
     * Delete a donation
     *
     * @param Donation $donation
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function delete(Donation $donation): bool {
        return DB::transaction(function () use ($donation) {
            // Only allow delete if status is pending or failed
            if (!in_array($donation->status, ['pending', 'failed'])) {
                throw new Exception('Can only delete donations with pending or failed status.');
            }

            return $donation->delete();
        });
    }

    /**
     * Mark donation as completed
     *
     * @param Donation $donation
     * @return Donation
     * @throws Throwable
     * @throws Throwable
     */
    public function markAsCompleted(Donation $donation): Donation {
        return DB::transaction(function () use ($donation) {
            $oldStatus = $donation->status;

            // Update status to completed
            $donation->update(['status' => 'completed']);

            // Dispatch event
            event(new DonationStatusEvent($donation, $oldStatus, 'completed'));

            // Update campaign total amount
            $this->campaignService->updateTotalAmount($donation->campaign);

            // Check if campaign goal reached
            $this->campaignService->checkGoalReached($donation->campaign);

            return $donation->fresh();
        });
    }

    /**
     * Mark donation as failed
     *
     * @param Donation $donation
     * @param string|null $reason
     * @return Donation
     * @throws Throwable
     * @throws Throwable
     */
    public function markAsFailed(Donation $donation, string $reason = null): Donation {
        return DB::transaction(function () use ($donation, $reason) {
            $oldStatus = $donation->status;

            // Update status to failed
            $updateData = ['status' => 'failed'];
            if ($reason) {
                $updateData['failure_reason'] = $reason;
            }

            $donation->update($updateData);

            // Dispatch event
            event(new DonationStatusEvent($donation, $oldStatus, 'failed'));

            return $donation->fresh();
        });
    }

    /**
     * Get donations for a campaign, optionally filtered by status
     *
     * @param int $campaignId
     * @param string|null $status
     * @return Collection
     */
    public function getDonationsForCampaign(int $campaignId, string $status = null): Collection {
        $query = Donation::where('campaign_id', $campaignId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->with('donor')->get();
    }

    /**
     * Get all donations by a user
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserDonations(int $userId): Collection {
        return Donation::where('donor_id', $userId)
            ->with('campaign')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total amount donated by a user (completed donations only)
     *
     * @param int $userId
     * @return float
     */
    public function getTotalDonatedByUser(int $userId): float {
        return Donation::where('donor_id', $userId)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Export donations for accounting with payment proofs
     *
     * @param array $filters
     * @return array
     */
    public function exportForAccounting(array $filters = []): array {
        $query = Donation::with(['donor', 'campaign', 'payment']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['campaign_id'])) {
            $query->where('campaign_id', $filters['campaign_id']);
        }

        if (isset($filters['donor_id'])) {
            $query->where('donor_id', $filters['donor_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (!($filters['include_anonymous'] ?? false)) {
            $query->where('is_anonymous', false);
        }

        // Get donations ordered by date
        $donations = $query->orderBy('created_at', 'desc')->get();

        // Format data for export
        $exportData = [];
        foreach ($donations as $donation) {
            $row = [
                'date' => $donation->created_at->format('Y-m-d H:i:s'),
                'donation_id' => $donation->id,
                'campaign' => $donation->campaign->title,
                'donor_name' => $donation->is_anonymous ? 'Anonymous' : $donation->donor->firstname.' '.$donation->donor->lastname,
                'donor_email' => $donation->is_anonymous ? '' : $donation->donor->email,
                'amount' => $donation->amount,
                'status' => $donation->status,
                'payment_method' => $donation->payment_method ?? '',
                'payment_reference' => $donation->payment?->transaction_reference ?? '',
                'payment_status' => $donation->payment?->status ?? '',
                'comment' => $donation->comment ?? '',
            ];

            $exportData[] = $row;
        }

        return [
            'data' => $exportData,
            'summary' => [
                'total_donations' => $donations->count(),
                'total_amount' => $donations->where('status', 'completed')->sum('amount'),
                'completed_count' => $donations->where('status', 'completed')->count(),
                'pending_count' => $donations->where('status', 'pending')->count(),
                'failed_count' => $donations->where('status', 'failed')->count(),
            ],
        ];
    }
}
