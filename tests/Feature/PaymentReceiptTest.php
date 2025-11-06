<?php

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Donation\Models\Donation;
use App\Modules\Payment\Models\Payment;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('can generate receipt for own completed payment', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $campaign = Campaign::factory()->create();
    $donation = Donation::factory()->create([
        'donor_id' => $user->id,
        'campaign_id' => $campaign->id,
        'status' => 'completed',
    ]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
        'transaction_reference' => 'TXN-TEST-123',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);
    $response->assertJsonStructure([
        'success',
        'receipt' => [
            'receipt_number',
            'payment' => [
                'id',
                'transaction_reference',
                'amount',
                'gateway',
                'status',
                'payment_date',
            ],
            'donation' => [
                'id',
                'amount',
                'status',
                'is_anonymous',
                'donation_date',
                'comment',
            ],
            'campaign' => [
                'title',
                'beneficiary',
            ],
            'donor' => [
                'name',
                'email',
            ],
            'generated_at',
        ],
    ]);
});

test('receipt includes correct receipt number format', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $campaign = Campaign::factory()->create();
    $donation = Donation::factory()->create([
        'donor_id' => $user->id,
        'campaign_id' => $campaign->id,
    ]);
    $payment = Payment::factory()->create([
        'id' => 5,
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $receipt = $response->json('receipt');
    expect($receipt['receipt_number'])->toBe('REC-00000005');
});

test('cannot generate receipt for pending payment', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'processing', // Not completed
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'Failed to generate receipt',
    ]);
});

test('cannot view receipt for someone elses payment', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->create(['donor_id' => $user2->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user2->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    // User1 tries to view User2's receipt
    Sanctum::actingAs($user1);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(403);
    $response->assertJson([
        'message' => 'You are not authorized to view this receipt.',
    ]);
});

test('admin can view any payment receipt', function () {
    $admin = User::factory()->create();
    $admin->givePermissionTo('view donations');

    $donor = User::factory()->create();

    $donation = Donation::factory()->create(['donor_id' => $donor->id]);
    $payment = Payment::factory()->create([
        'user_id' => $donor->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    // Admin tries to view donor's receipt
    Sanctum::actingAs($admin);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

test('receipt shows anonymous for anonymous donation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

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

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $receipt = $response->json('receipt');

    expect($receipt['donor']['name'])->toBe('Anonymous Donor');
    expect($receipt['donor']['email'])->toBe('');
});

test('receipt includes payment transaction reference', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
        'transaction_reference' => 'TXN-ABC123DEF456',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $receipt = $response->json('receipt');

    expect($receipt['payment']['transaction_reference'])->toBe('TXN-ABC123DEF456');
});

test('receipt includes campaign beneficiary information', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $campaign = Campaign::factory()->create([
        'title' => 'Help Build Schools',
        'beneficiary_name' => 'Education Foundation',
    ]);
    $donation = Donation::factory()->create([
        'donor_id' => $user->id,
        'campaign_id' => $campaign->id,
    ]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $receipt = $response->json('receipt');

    expect($receipt['campaign']['title'])->toBe('Help Build Schools');
    expect($receipt['campaign']['beneficiary'])->toBe('Education Foundation');
});

test('receipt includes generation timestamp', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create(['donor_id' => $user->id]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $receipt = $response->json('receipt');

    expect($receipt['generated_at'])->not->toBeNull();
    expect($receipt['generated_at'])->toMatch('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/');
});

test('cannot generate receipt without authentication', function () {
    $donation = Donation::factory()->create();
    $payment = Payment::factory()->create([
        'donation_id' => $donation->id,
        'status' => 'completed',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(401);
});

test('receipt amount matches payment amount', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $donation = Donation::factory()->create([
        'donor_id' => $user->id,
        'amount' => 150.00,
    ]);
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'donation_id' => $donation->id,
        'amount' => 150.00,
        'status' => 'completed',
    ]);

    $response = $this->getJson("/api/v1/payments/{$payment->id}/receipt");

    $response->assertStatus(200);
    $receipt = $response->json('receipt');

    expect((float) $receipt['payment']['amount'])->toBe(150.00);
    expect((float) $receipt['donation']['amount'])->toBe(150.00);
});
