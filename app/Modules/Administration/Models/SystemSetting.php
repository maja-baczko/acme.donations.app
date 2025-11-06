<?php

namespace App\Modules\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model {
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected function casts(): array {
        return [
            'is_public' => 'boolean',
        ];
    }
}
