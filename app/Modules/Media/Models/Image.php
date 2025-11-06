<?php

namespace App\Modules\Media\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model {
    use HasFactory;

    // Create a new factory instance for the model
    protected static function newFactory(): ImageFactory {
        return ImageFactory::new();
    }

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
