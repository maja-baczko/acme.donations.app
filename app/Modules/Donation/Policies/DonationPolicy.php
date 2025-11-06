<?php

namespace App\Modules\Donation\Policies;

use App\Modules\Donation\Models\Donation;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DonationPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user): bool {
        return $user->hasPermissionTo('view donations');
    }

    public function view(User $user, Donation $donation): bool {
        // Can view if has permission OR is the donor
        return $user->hasPermissionTo('view donations') || $donation->donor_id === $user->id;
    }

    public function create(User $user): bool {
        // All authenticated users can donate, OR user has create permission
        return $user->hasPermissionTo('create donations') || auth()->check();
    }

    public function update(User $user, Donation $donation): bool {
        // Can update if has permission OR (is the donor AND donation is still pending)
        if ($user->hasPermissionTo('edit donations')) {
            return true;
        }

        return $donation->donor_id === $user->id && $donation->status === 'pending';
    }

    public function delete(User $user, Donation $donation): bool {
        // Can only delete if has permission AND donation is not completed
        return $user->hasPermissionTo('delete donations') && $donation->status !== 'completed';
    }

    public function restore(User $user): bool {
        return $user->hasPermissionTo('delete donations');
    }

    public function forceDelete(User $user): bool {
        return $user->hasPermissionTo('delete donations');
    }
}
