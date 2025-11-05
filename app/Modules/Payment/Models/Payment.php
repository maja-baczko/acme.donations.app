<?php

namespace App\Modules\Payment\Models;

use App\Modules\Donation\Models\Donation;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model {
	use HasFactory;

    protected $fillable = [
        'donation_id',
        'user_id',
        'amount',
        'status',          // pending | completed | failed
        'gateway',         // mock, stripe, molly
        'transaction_reference',  // reference issued by the payment provider
    ];

	public function donation(): BelongsTo {
		return $this->belongsTo(Donation::class);
	}

	public function user(): BelongsTo {
		return $this->belongsTo(User::class);
	}
}
