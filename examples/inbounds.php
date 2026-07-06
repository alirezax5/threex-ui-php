<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// List all inbounds
$inbounds = $panel->inbounds()->list();
echo "Total inbounds: " . count($inbounds['obj'] ?? []) . "\n";

// List slim (lightweight)
$slim = $panel->inbounds()->listSlim();

// Get a specific inbound
$inbound = $panel->inbounds()->get(1);
echo "Inbound remark: " . ($inbound['obj']['remark'] ?? '') . "\n";

// Create a new VLESS inbound
$newInbound = $panel->inbounds()->add([
    'remark'   => 'My VLESS Inbound',
    'listen'   => '',
    'port'     => 443,
    'protocol' => 'vless',
    'enable'   => true,
    'total'    => 0,
    'expiryTime' => 0,
    'settings' => json_encode([
        'clients'    => [],
        'decryption' => 'none',
        'fallbacks'  => [],
    ]),
    'streamSettings' => json_encode([
        'network'  => 'tcp',
        'security' => 'none',
    ]),
    'sniffing' => json_encode([
        'enabled'      => true,
        'destOverride' => ['http', 'tls'],
    ]),
]);
echo "Created inbound ID: " . ($newInbound['obj']['id'] ?? '') . "\n";

// Toggle enable/disable
$panel->inbounds()->setEnable(1, false);

// Reset traffic for an inbound
$panel->inbounds()->resetTraffic(1);

// Delete an inbound
$panel->inbounds()->delete(1);

// Import inbound config from JSON
$panel->inbounds()->import('{"remark":"imported","port":8080,"protocol":"vmess",...}');

// Manage fallbacks
$fallbacks = $panel->inbounds()->getFallbacks(1);
$panel->inbounds()->setFallbacks(1, [
    ['path' => '/ws', 'dest' => '@xray-dest', 'xver' => 1],
]);
