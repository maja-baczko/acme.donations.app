<?php

namespace App\Modules\Media\Policies;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Media\Models\Image;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy {
    use HandlesAuthorization;

    public function viewAny(): bool {
        // Any authenticated user can view images
        return true;
    }

    public function view(): bool {
        // Any authenticated user can view an image
        return true;
    }

    public function create(): bool {
        // Check if user is admin with relevant permissions
        return true;
    }

    public function update(User $user, Image $image): bool {
        // Check if user is admin with relevant permissions
        if ($user->hasPermissionTo('edit campaigns') || $user->hasPermissionTo('edit users')) {
            return true;
        }

        // Check ownership based on entity type
        return $this->isOwner($user, $image);
    }

    public function delete(User $user, Image $image): bool {
        // Check if user is admin with relevant permissions
        if ($user->hasPermissionTo('edit campaigns') || $user->hasPermissionTo('edit users')) {
            return true;
        }

        // Check ownership based on entity type
        return $this->isOwner($user, $image);
    }

    public function restore(User $user): bool {
        return $user->hasPermissionTo('edit campaigns') || $user->hasPermissionTo('edit users');
    }

    public function forceDelete(User $user): bool {
        return $user->hasPermissionTo('edit campaigns') || $user->hasPermissionTo('edit users');
    }

    /**
     * Check if the user owns the entity that the image is attached to
     */
    private function isOwner(User $user, Image $image): bool {
        if (!$image->entity_type || !$image->entity_id) {
            return false;
        }

        // Check ownership based on entity type
        if ($image->entity_type === 'App\\Modules\\Campaign\\Models\\Campaign') {
            $campaign = Campaign::find($image->entity_id);
            return $campaign && $campaign->creator_id === $user->id;
        }

        if ($image->entity_type === 'App\\Modules\\User\\Models\\User') {
            return $image->entity_id === $user->id;
        }

        return false;
    }
}
