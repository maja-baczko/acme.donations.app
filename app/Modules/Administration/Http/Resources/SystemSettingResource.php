<?php

namespace App\Modules\Administration\Http\Resources;

use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SystemSetting */
class SystemSettingResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'type' => $this->type,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
