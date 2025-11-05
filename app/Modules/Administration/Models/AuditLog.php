<?php

namespace App\Modules\Administration\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }
}
