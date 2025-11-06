<?php

namespace App\Modules\Campaign\Models;

use App\Modules\Media\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model {
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
