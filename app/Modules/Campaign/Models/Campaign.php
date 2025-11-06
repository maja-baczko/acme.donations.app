<?php

namespace App\Modules\Campaign\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model {
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'category_id',
        'title',
        'slug',
        'description',
        'goal_amount',
        'current_amount',
        'status',
        'start_date',
        'end_date',
        'beneficiary_name',
        'beneficiary_website',
        'featured',
    ];

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array {
        return [
            'start_date' => 'timestamp',
            'end_date' => 'timestamp',
            'featured' => 'boolean',
        ];
    }
}
