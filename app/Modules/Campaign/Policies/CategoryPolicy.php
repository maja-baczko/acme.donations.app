<?php

namespace App\Modules\Campaign\Policies;

use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy {
    use HandlesAuthorization;

    public function viewAny(): bool {
        // Anyone can view categories
        return true;
    }

    public function view(): bool {
        // Anyone can view a category
        return true;
    }

    public function create(User $user): bool {
        return $user->hasPermissionTo('edit campaigns');
    }

    public function update(User $user): bool {
        return $user->hasPermissionTo('edit campaigns');
    }

    public function delete(User $user): bool {
        return $user->hasPermissionTo('edit campaigns');
    }

    public function restore(User $user): bool {
        return $user->hasPermissionTo('edit campaigns');
    }

    public function forceDelete(User $user): bool {
        return $user->hasPermissionTo('edit campaigns');
    }
}
