<?php

namespace App\Modules\Payment\Http\Resources;

use App\Modules\Payment\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Payment */
class PaymentResource extends JsonResource {
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'amount' => $this->amount,
			'status' => $this->status,
			'gateway' => $this->gateway,
			'transaction_reference' => $this->transaction_reference,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,

			'donation_id' => $this->donation_id,
			'user_id' => $this->user_id,
		];
	}
}
