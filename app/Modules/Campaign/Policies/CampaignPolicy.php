<?php

namespace App\Modules\Campaign\Policies;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user): bool {
        return $user->hasPermissionTo('view campaigns');
    }

    public function view(User $user, Campaign $campaign): bool {
        // Can view if has permission OR is the campaign creator
        return $user->hasPermissionTo('view campaigns') || $campaign->creator_id === $user->id;
    }

    public function create(User $user): bool {
        return $user->hasPermissionTo('create campaigns');
    }

    public function update(User $user, Campaign $campaign): bool {
        // Can update if has permission OR is the campaign creator
        return $user->hasPermissionTo('edit campaigns') || $campaign->creator_id === $user->id;
    }

    public function delete(User $user, Campaign $campaign): bool {
        // Check permission or ownership first
        $hasPermission = $user->hasPermissionTo('delete campaigns') || $campaign->creator_id === $user->id;

        if (!$hasPermission) {
            return false;
        }

        // Prevent deletion if campaign has completed donations
        $hasCompletedDonations = $campaign->donations()->where('status', 'completed')->exists();

        return !$hasCompletedDonations;
    }

    public function restore(User $user): bool {
        return $user->hasPermissionTo('delete campaigns');
    }

    public function forceDelete(User $user): bool {
        return $user->hasPermissionTo('delete campaigns');
    }
}
