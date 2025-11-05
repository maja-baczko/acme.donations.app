<?php

namespace App\Modules\User\Policies;

use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user): bool {
        return $user->hasPermissionTo('view users');
    }

    public function view(User $user, User $model): bool {
        return $user->hasPermissionTo('view users');
    }

    public function create(User $user): bool {
        return $user->hasPermissionTo('create users');
    }

    public function update(User $user, User $model): bool {
        return $user->hasPermissionTo('edit users');
    }

    public function delete(User $user, User $model): bool {
        // Prevent users from deleting themselves
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermissionTo('delete users');
    }

    public function restore(User $user, User $model): bool {
        return $user->hasPermissionTo('delete users');
    }

    public function forceDelete(User $user, User $model): bool {
        return $user->hasPermissionTo('delete users');
    }
}
