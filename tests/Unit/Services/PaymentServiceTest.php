<?php

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Donation\Models\Donation;
use App\Modules\Payment\Events\PaymentCompletedEvent;
use App\Modules\Payment\Events\PaymentFailedEvent;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(PaymentService::class);
});

test('can create payment with auto user id', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $donation = Donation::factory()->create();

    $data = [
        'donation_id' => $donation->id,
        'amount' => 100.00,
        'gateway' => 'stripe',
    ];

    $payment = $this->service->create($data);

    expect($payment)->toBeInstanceOf(Payment::class);
    expect($payment->user_id)->toBe($user->id);
    expect($payment->status)->toBe('processing');
    expect($payment->transaction_reference)->not->toBeNull();
});

test('payment generates unique transaction reference', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $donation = Donation::factory()->create();

    $payment1 = $this->service->create([
        'donation_id' => $donation->id,
        'amount' => 50.00,
        'gateway' => 'mock',
    ]);

    $payment2 = $this->service->create([
        'donation_id' => $donation->id,
        'amount' => 75.00,
        'gateway' => 'mock',
    ]);

    expect($payment1->transaction_reference)->not->toBe($payment2->transaction_reference);
    expect($payment1->transaction_reference)->toStartWith('TXN-');
    expect($payment2->transaction_reference)->toStartWith('TXN-');
});

test('marking payment as completed dispatches event', function () {
    Event::fake();

    $payment = Payment::factory()->create(['status' => 'processing']);

    $this->service->markAsCompleted($payment, 'TXN-COMPLETED-123');

    $payment->refresh();
    expect($payment->status)->toBe('completed');
    expect($payment->transaction_reference)->toBe('TXN-COMPLETED-123');

    Event::assertDispatched(PaymentCompletedEvent::class, function ($event) use ($payment) {
        return $event->payment->id === $payment->id;
    });
});

test('marking payment as failed dispatches event', function () {
    Event::fake();

    $payment = Payment::factory()->create(['status' => 'processing']);

    $this->service->markAsFailed($payment, 'Card declined');

    $payment->refresh();
    expect($payment->status)->toBe('failed');

    Event::assertDispatched(PaymentFailedEvent::class, function ($event) use ($payment) {
        return $event->payment->id === $payment->id
            && $event->errorMessage === 'Card declined';
    });
});

test('cannot delete completed payment', function () {
    $payment = Payment::factory()->create(['status' => 'completed']);

    expect(fn () => $this->service->delete($payment))
        ->toThrow(Exception::class, 'Cannot delete completed payment');
});

test('can delete processing payment', function () {
    $payment = Payment::factory()->create(['status' => 'processing']);

    $result = $this->service->delete($payment);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
});

test('can get payments by status', function () {
    Payment::factory()->count(5)->create(['status' => 'completed']);
    Payment::factory()->count(3)->create(['status' => 'processing']);

    $completedPayments = $this->service->getPaymentsByStatus('completed');

    expect($completedPayments)->toHaveCount(5);
});

test('can get user payments', function () {
    $user = User::factory()->create();
    Payment::factory()->count(4)->create(['user_id' => $user->id]);
    Payment::factory()->count(2)->create(); // Other users

    $payments = $this->service->getUserPayments($user->id);

    expect($payments)->toHaveCount(4);
});

test('can retry failed payment', function () {
    $payment = Payment::factory()->create(['status' => 'failed']);

    $retriedPayment = $this->service->retryPayment($payment);

    $retriedPayment->refresh();
    expect($retriedPayment->status)->toBe('processing');
});

test('cannot retry completed payment', function () {
    $payment = Payment::factory()->create(['status' => 'completed']);

    expect(fn () => $this->service->retryPayment($payment))
        ->toThrow(Exception::class, 'Can only retry failed payments');
});

test('can generate receipt for completed payment', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create([
        'title' => 'Test Campaign',
        'beneficiary_name' => 'Test Beneficiary',
    ]);
    $donation = Donation::factory()->create([
        'donor_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 100.00,
        'status' => 'completed',
    ]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
        'amount' => 100.00,
        'transaction_reference' => 'TXN-TEST-123',
    ]);

    $receipt = $this->service->generateReceipt($payment);

    expect($receipt)->toBeArray();
    expect($receipt)->toHaveKey('receipt_number');
    expect($receipt)->toHaveKey('payment');
    expect($receipt)->toHaveKey('donation');
    expect($receipt)->toHaveKey('campaign');
    expect($receipt)->toHaveKey('donor');
    expect($receipt['payment']['transaction_reference'])->toBe('TXN-TEST-123');
});

test('cannot generate receipt for pending payment', function () {
    $payment = Payment::factory()->create(['status' => 'processing']);

    expect(fn () => $this->service->generateReceipt($payment))
        ->toThrow(Exception::class, 'Can only generate receipts for completed payments');
});

test('receipt shows anonymous for anonymous donations', function () {
    $user = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
    $campaign = Campaign::factory()->create();
    $donation = Donation::factory()->create([
        'donor_id' => $user->id,
        'campaign_id' => $campaign->id,
        'is_anonymous' => true,
        'status' => 'completed',
    ]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    $receipt = $this->service->generateReceipt($payment);

    expect($receipt['donor']['name'])->toBe('Anonymous Donor');
    expect($receipt['donor']['email'])->toBe('');
});

test('receipt number has correct format', function () {
    $payment = Payment::factory()->create([
        'id' => 42,
        'status' => 'completed',
    ]);

    $receipt = $this->service->generateReceipt($payment);

    expect($receipt['receipt_number'])->toBe('REC-00000042');
});
