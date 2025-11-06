<?php

namespace App\Modules\Administration\Policies;

use App\Modules\Administration\Models\SystemSetting;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemSettingPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user): bool {
        return $user->hasPermissionTo('view system settings');
    }

    public function view(User $user, SystemSetting $systemSetting): bool {
        // Can view if has permission OR setting is public
        return $user->hasPermissionTo('view system settings') || $systemSetting->is_public === true;
    }

    public function create(User $user): bool {
        return $user->hasPermissionTo('edit system settings');
    }

    public function update(User $user): bool {
        return $user->hasPermissionTo('edit system settings');
    }

    public function delete(User $user): bool {
        return $user->hasPermissionTo('edit system settings');
    }

    public function restore(User $user): bool {
        return $user->hasPermissionTo('edit system settings');
    }

    public function forceDelete(User $user): bool {
        return $user->hasPermissionTo('edit system settings');
    }
}
