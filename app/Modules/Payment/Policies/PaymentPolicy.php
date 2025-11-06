<?php

namespace App\Modules\Payment\Policies;

use App\Modules\Payment\Models\Payment;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user): bool {
        // Payments are tied to donations
        return $user->hasPermissionTo('view donations');
    }

    public function view(User $user, Payment $payment): bool {
        // Can view if has permission OR is the payment owner
        return $user->hasPermissionTo('view donations') || $payment->user_id === $user->id;
    }

    public function create(User $user): bool {
        // Anyone authenticated can create payment for their donation, OR user has create permission
        return $user->hasPermissionTo('create donations') || auth()->check();
    }

    public function delete(User $user, Payment $payment): bool {
        // Can only delete if has permission AND payment is not completed
        return $user->hasPermissionTo('delete donations') && $payment->status !== 'completed';
    }

    public function restore(User $user): bool {
        return $user->hasPermissionTo('delete donations');
    }

    public function forceDelete(User $user): bool {
        return $user->hasPermissionTo('delete donations');
    }

    /**
     * Determine if the user can view a payment receipt
     *
     * @param User $user
     * @param Payment $payment
     * @return bool
     */
    public function viewReceipt(User $user, Payment $payment): bool {
        // User can view receipt if:
        // 1. They are the payment owner
        // 2. They have 'view donations' permission (admin/accounting)
        return $user->id === $payment->user_id || $user->hasPermissionTo('view donations');
    }
}
