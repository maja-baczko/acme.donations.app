<?php

namespace App\Modules\Administration\Http\Resources;

use App\Modules\Administration\Models\AuditLog;
use App\Modules\User\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AuditLog */
class AuditLogResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user_id' => $this->user_id,

            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
