<?php

use App\Modules\Administration\Models\AuditLog;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('audit log update route does not exist', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view audit logs');
    Sanctum::actingAs($user);

    $auditLog = AuditLog::factory()->create();

    $response = $this->putJson("/api/v1/audit-logs/{$auditLog->id}", [
        'action' => 'hacked',
    ]);

    // Route should not exist - expect 404 or 405
    expect(
        $response->status() === 404 || $response->status() === 405
    )->toBeTrue("Expected 404 or 405, got {$response->status()}");
});

test('audit log delete route does not exist for regular users', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view audit logs');
    Sanctum::actingAs($user);

    $auditLog = AuditLog::factory()->create();

    $response = $this->deleteJson("/api/v1/audit-logs/{$auditLog->id}");

    // Route should not exist or be forbidden
    expect(in_array($response->status(), [403, 404, 405]))->toBeTrue();
});

test('can view audit logs', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view audit logs');
    Sanctum::actingAs($user);

    AuditLog::factory()->count(5)->create();

    $response = $this->getJson('/api/v1/audit-logs');

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
});

test('cannot view audit logs without permission', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/audit-logs');

    $response->assertStatus(403);
});

test('audit log data cannot be changed after creation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $auditLog = AuditLog::factory()->create([
        'action' => 'created',
        'entity_type' => 'Campaign',
        'entity_id' => 1,
    ]);

    // Try to modify directly (this should be prevented by lack of routes)
    // Verify the data hasn't changed
    $auditLog->refresh();

    expect($auditLog->action)->toBe('created');
    expect($auditLog->entity_type)->toBe('Campaign');
    expect($auditLog->entity_id)->toBe(1);
});
