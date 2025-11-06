<?php

namespace App\Modules\User\Http\Resources;

use App\Modules\Media\Http\Resources\ImageResource;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'department' => $this->department,
            'function' => $this->function,
            'still_working' => $this->still_working,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'profile' => new ImageResource($this->whenLoaded('profile')),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'permissions' => $this->whenLoaded('permissions', fn () => $this->permissions->pluck('name')),
        ];
    }
}
