<?php

namespace App\Modules\Donation\Models;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Payment\Models\Payment;
use App\Modules\User\Models\User;
use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model {
    use HasFactory;

    // Create a new factory instance for the model
    protected static function newFactory(): DonationFactory {
        return DonationFactory::new();
    }

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

    public function payment(): HasOne {
        return $this->hasOne(Payment::class);
    }
}
