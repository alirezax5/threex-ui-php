<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// ─── List clients (paginated) ────────────────────────────────────
$page = $panel->clients()->listPaged([
    'page'     => 1,
    'pageSize' => 50,
    'search'   => '',
]);
echo "Total clients: " . ($page['obj']['total'] ?? 0) . "\n";

// ─── List all clients with traffic ───────────────────────────────
$all = $panel->clients()->list();

// ─── Add a new client ────────────────────────────────────────────
$result = $panel->clients()->add(
    [
        'email'      => 'user@example.com',
        'totalGB'    => 100,
        'expiryTime' => 0,
        'enable'     => true,
    ],
    [1, 2] // inbound IDs
);
echo "Client added: " . ($result['success'] ? 'yes' : 'no') . "\n";

// ─── Get client details ──────────────────────────────────────────
$client = $panel->clients()->get('user@example.com');

// ─── Update client ───────────────────────────────────────────────
$panel->clients()->update('user@example.com', [
    'email'   => 'user@example.com',
    'enable'  => false,
]);

// ─── Get client traffic ──────────────────────────────────────────
$traffic = $panel->clients()->traffic('user@example.com');
echo "Upload: " . ($traffic['obj']['up'] ?? 0) . " bytes\n";
echo "Download: " . ($traffic['obj']['down'] ?? 0) . " bytes\n";

// ─── Reset client traffic ────────────────────────────────────────
$panel->clients()->resetTraffic('user@example.com');

// ─── Get connection links ────────────────────────────────────────
$links = $panel->clients()->links('user@example.com');

// ─── Online clients ──────────────────────────────────────────────
$onlines = $panel->clients()->onlines();
echo "Online clients: " . implode(', ', $onlines['obj'] ?? []) . "\n";

// ─── Delete client ───────────────────────────────────────────────
$panel->clients()->delete('user@example.com');

// ─── Bulk operations ─────────────────────────────────────────────
$panel->clients()->bulkCreate([
    [
        'client'     => ['email' => 'bulk1@test.com', 'totalGB' => 50],
        'inboundIds' => [1],
    ],
    [
        'client'     => ['email' => 'bulk2@test.com', 'totalGB' => 100],
        'inboundIds' => [1],
    ],
]);

$panel->clients()->bulkDelete(['bulk1@test.com', 'bulk2@test.com']);

// ─── Client groups ───────────────────────────────────────────────
$panel->clientGroups()->create('premium-users');
$panel->clientGroups()->bulkAdd(['user1@test.com', 'user2@test.com'], 'premium-users');
$emails = $panel->clientGroups()->emails('premium-users');
$panel->clientGroups()->bulkRemove(['user1@test.com']);
$panel->clientGroups()->delete('premium-users');
