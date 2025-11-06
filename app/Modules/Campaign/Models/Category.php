<?php

namespace App\Modules\Campaign\Models;

use App\Modules\Media\Models\Image;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model {
    use HasFactory;

    protected static function newFactory(): CategoryFactory {
        return CategoryFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'is_active',
    ];

    public function icon(): BelongsTo {
        return $this->belongsTo(Image::class, 'icon');
    }

    public function campaigns(): HasMany {
        return $this->hasMany(Campaign::class);
    }

    protected function casts(): array {
        return [
            'is_active' => 'boolean',
        ];
    }
}
