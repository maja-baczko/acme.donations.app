<?php

namespace App\Modules\Donation\Models;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model {
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'donor_id',
        'amount',
        'status',
    ];

    public function campaign(): BelongsTo {
        return $this->belongsTo(Campaign::class);
    }

    public function donor(): BelongsTo {
        return $this->belongsTo(User::class, 'donor_id');
    }
}
