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
            'password' => Hash::make($this->password),
			'department' => $this->department,
			'function' => $this->function,
			'still_working' => $this->still_working,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,

			'role_id' => $this->role_id,
			'profile' => $this->profile,

			'role' => new RoleResource($this->whenLoaded('role')),
			'profile' => new ImageResource($this->whenLoaded('profile')),
		];
	}
}
