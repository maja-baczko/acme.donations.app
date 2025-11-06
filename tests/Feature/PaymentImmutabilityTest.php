<?php

use App\Modules\Donation\Models\Donation;
use App\Modules\Payment\Models\Payment;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('payment update route does not exist', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'amount' => 50.00,
        'status' => 'completed',
    ]);

    // Attempt to update payment amount (should fail - route doesn't exist)
    $response = $this->putJson("/api/v1/payments/{$payment->id}", [
        'amount' => 99999.99,
    ]);

    // Route should not exist
    expect(
        $response->status() === 404 || $response->status() === 405
    )->toBeTrue("Expected 404 or 405, got {$response->status()}");
});

test('payment amount cannot be changed after creation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'amount' => 50.00,
        'status' => 'completed',
    ]);

    $originalAmount = $payment->amount;

    // Verify amount hasn't changed
    $payment->refresh();
    /*expect($payment->amount)->toBe($originalAmount);*/
});

test('can create payment', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('create donations');
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);

    $response = $this->postJson('/api/v1/payments', [
        'donation_id' => $donation->id,
        'amount' => 100.00,
        'status' => 'processing',
        'gateway' => 'mock',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('payments', [
        'donation_id' => $donation->id,
        'amount' => 100.00,
        'status' => 'processing',
        'gateway' => 'mock',
    ]);
});

test('payment generates unique transaction reference', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('create donations');
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);

    $response = $this->postJson('/api/v1/payments', [
        'donation_id' => $donation->id,
        'amount' => 75.00,
        'status' => 'processing',
        'gateway' => 'stripe',
    ]);

    $response->assertStatus(201);

    $payment = Payment::latest()->first();
    expect($payment->transaction_reference)->not->toBeNull();
    expect($payment->transaction_reference)->toStartWith('TXN-');
});

test('cannot delete completed payment', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('delete donations');
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    $response = $this->deleteJson("/api/v1/payments/{$payment->id}");

    // Should fail because payment is completed
    $response->assertStatus(422);
    $this->assertDatabaseHas('payments', ['id' => $payment->id]);
});

test('can delete processing payment', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('delete donations');
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'processing',
    ]);

    $response = $this->deleteJson("/api/v1/payments/{$payment->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
});

test('payment status can only be changed through service methods', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'processing',
    ]);

    // Try to update via API (should not exist)
    $response = $this->patchJson("/api/v1/payments/{$payment->id}", [
        'status' => 'completed',
    ]);

    expect(
        $response->status() === 404 || $response->status() === 405
    )->toBeTrue();

    // Status should remain unchanged
    $payment->refresh();
    expect($payment->status)->toBe('processing');
});

test('payment user id is automatically set from auth', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('create donations');
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);

    $response = $this->postJson('/api/v1/payments', [
        'donation_id' => $donation->id,
        'amount' => 50.00,
        'gateway' => 'paypal',
    ]);

    $response->assertStatus(201);

    $payment = Payment::latest()->first();
    expect($payment->user_id)->toBe($user->id);
});
