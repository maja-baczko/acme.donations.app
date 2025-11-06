<?php

namespace App\Modules\Payment\Http\Controllers;

use App\Modules\Payment\Http\Resources\PaymentResource;
use App\Modules\Payment\Models\Payment;
use Illuminate\Http\Request;

class PaymentController {
    public function index() {
        return PaymentResource::collection(Payment::all());
    }

    public function create(Request $request) {
        $data = $request->validate([
            'donation_id' => ['required', 'exists:donations'],
            'user_id' => ['required', 'exists:users'],
            'amount' => ['required'],
            'status' => ['required'],
            'gateway' => ['required'],
            'transaction_reference' => ['required'],
        ]);

        return new PaymentResource(Payment::create($data));
    }

    public function show(Payment $payment) {
        return new PaymentResource($payment);
    }

    public function update(Request $request, Payment $payment) {
        $data = $request->validate([
            'donation_id' => ['required', 'exists:donations'],
            'user_id' => ['required', 'exists:users'],
            'amount' => ['required'],
            'status' => ['required'],
            'gateway' => ['required'],
            'transaction_reference' => ['required'],
        ]);

        $payment->update($data);

        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment) {
        $payment->delete();

        return response()->json();
    }
}
