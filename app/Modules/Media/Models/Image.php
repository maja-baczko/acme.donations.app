<?php

namespace App\Modules\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model {
    use HasFactory;

    protected $fillable = [
        'type',
        'entity_type',
        'entity_id',
        'file_path',
        'file_name',
        'is_primary',
        'alt_text',
    ];

    protected function casts(): array {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
