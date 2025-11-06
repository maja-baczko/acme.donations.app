<?php

namespace App\Modules\Administration\Policies;

use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditLogPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user): bool {
        return $user->hasPermissionTo('view audit logs');
    }

    public function view(User $user): bool {
        return $user->hasPermissionTo('view audit logs');
    }
}
