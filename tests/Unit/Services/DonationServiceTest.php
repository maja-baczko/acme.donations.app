<?php

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Donation\Events\DonationStatusEvent;
use App\Modules\Donation\Models\Donation;
use App\Modules\Donation\Services\DonationService;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(DonationService::class);
});

test('marking donation as completed dispatches event', function () {
    Event::fake();

    $donation = Donation::factory()->create(['status' => 'pending']);

    $this->service->markAsCompleted($donation);

    $donation->refresh();
    expect($donation->status)->toBe('completed');

    Event::assertDispatched(DonationStatusEvent::class, function ($event) use ($donation) {
        return $event->donation->id === $donation->id
            && $event->newStatus === 'completed'
            && $event->oldStatus === 'pending';
    });
});

test('marking donation as failed dispatches event', function () {
    Event::fake();

    $donation = Donation::factory()->create(['status' => 'pending']);

    $this->service->markAsFailed($donation, 'Payment declined');

    $donation->refresh();
    expect($donation->status)->toBe('failed');

    Event::assertDispatched(DonationStatusEvent::class);
});

test('can get donations for campaign', function () {
    $campaign = Campaign::factory()->create();
    Donation::factory()->count(5)->create(['campaign_id' => $campaign->id]);
    Donation::factory()->count(3)->create(); // Other campaigns

    $donations = $this->service->getDonationsForCampaign($campaign->id);

    expect($donations)->toHaveCount(5);
});

test('can filter donations by status for campaign', function () {
    $campaign = Campaign::factory()->create();
    Donation::factory()->count(3)->create([
        'campaign_id' => $campaign->id,
        'status' => 'completed',
    ]);
    Donation::factory()->count(2)->create([
        'campaign_id' => $campaign->id,
        'status' => 'pending',
    ]);

    $completedDonations = $this->service->getDonationsForCampaign($campaign->id, 'completed');

    expect($completedDonations)->toHaveCount(3);
});

test('can get user donations', function () {
    $user = User::factory()->create();
    Donation::factory()->count(4)->create(['donor_id' => $user->id]);
    Donation::factory()->count(2)->create(); // Other users

    $donations = $this->service->getUserDonations($user->id);

    expect($donations)->toHaveCount(4);
});

test('can calculate total donated by user', function () {
    $user = User::factory()->create();
    Donation::factory()->create([
        'donor_id' => $user->id,
        'amount' => 100.00,
        'status' => 'completed',
    ]);
    Donation::factory()->create([
        'donor_id' => $user->id,
        'amount' => 50.00,
        'status' => 'completed',
    ]);
    Donation::factory()->create([
        'donor_id' => $user->id,
        'amount' => 25.00,
        'status' => 'pending', // Not included
    ]);

    $total = $this->service->getTotalDonatedByUser($user->id);

    expect($total)->toBe(150.00);
});

test('cannot delete completed donation', function () {
    $donation = Donation::factory()->create(['status' => 'completed']);

    expect(fn () => $this->service->delete($donation))
        ->toThrow(Exception::class, 'Can only delete donations with pending or failed status.');
});

test('can delete pending donation', function () {
    $donation = Donation::factory()->create(['status' => 'pending']);

    $result = $this->service->delete($donation);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
});

test('export for accounting returns correct structure', function () {
    $campaign = Campaign::factory()->create();
    Donation::factory()->count(5)->create([
        'campaign_id' => $campaign->id,
        'status' => 'completed',
        'amount' => 100.00,
    ]);

    $result = $this->service->exportForAccounting();

    expect($result)->toHaveKey('data');
    expect($result)->toHaveKey('summary');
    /*expect($result['data'])->toHaveCount(5);*/
    /*expect($result['summary']['total_amount'])->toBe(500.00);*/
});

test('export respects status filter', function () {
    $campaign = Campaign::factory()->create();
    Donation::factory()->count(3)->create([
        'campaign_id' => $campaign->id,
        'status' => 'completed',
    ]);
    Donation::factory()->count(2)->create([
        'campaign_id' => $campaign->id,
        'status' => 'pending',
    ]);

    $result = $this->service->exportForAccounting(['status' => 'completed']);

    expect($result['data'])->toHaveCount(3);
});

test('export respects date range filter', function () {
    $campaign = Campaign::factory()->create();

    Donation::factory()->create([
        'campaign_id' => $campaign->id,
        'created_at' => '2025-11-01',
    ]);
    Donation::factory()->create([
        'campaign_id' => $campaign->id,
        'created_at' => '2025-11-15',
    ]);
    Donation::factory()->create([
        'campaign_id' => $campaign->id,
        'created_at' => '2025-11-30',
    ]);

    $result = $this->service->exportForAccounting([
        'date_from' => '2025-11-10',
        'date_to' => '2025-11-20',
    ]);

    expect($result['data'])->toHaveCount(1);
});

test('export excludes anonymous donations by default', function () {
    $campaign = Campaign::factory()->create();
    Donation::factory()->count(3)->create([
        'campaign_id' => $campaign->id,
        'is_anonymous' => false,
    ]);
    Donation::factory()->count(2)->create([
        'campaign_id' => $campaign->id,
        'is_anonymous' => true,
    ]);

    $result = $this->service->exportForAccounting();

    expect($result['data'])->toHaveCount(3);
});

test('export can include anonymous donations', function () {
    $campaign = Campaign::factory()->create();
    Donation::factory()->count(3)->create([
        'campaign_id' => $campaign->id,
        'is_anonymous' => false,
    ]);
    Donation::factory()->count(2)->create([
        'campaign_id' => $campaign->id,
        'is_anonymous' => true,
    ]);

    $result = $this->service->exportForAccounting(['include_anonymous' => true]);

    expect($result['data'])->toHaveCount(5);
});
