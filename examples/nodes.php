<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// ─── List all nodes ──────────────────────────────────────────────
$nodes = $panel->nodes()->list();
foreach ($nodes['obj'] ?? [] as $node) {
    echo "Node #{$node['id']}: {$node['name']} ({$node['scheme']}://{$node['address']}:{$node['port']})\n";
    echo "  Enabled: " . ($node['enable'] ? 'Yes' : 'No') . "\n";
    echo "  Protocol: {$node['protocol']}\n";
    echo "  Uplink: " . bytes_to_human($node['up'] ?? 0) . " | Downlink: " . bytes_to_human($node['down'] ?? 0) . "\n";
}

// ─── Add a new node ──────────────────────────────────────────────
$result = $panel->nodes()->add([
    'name'      => 'Germany Node',
    'scheme'    => 'https',
    'address'   => 'germany.example.com',
    'port'      => 54321,
    'basePath'  => '/',
    'apiToken'  => 'api-token-of-remote-panel',
    'enable'    => true,
]);
echo "Node created: " . ($result['success'] ? 'yes' : 'no') . "\n";

// ─── Get node details ────────────────────────────────────────────
$nodeId = $result['obj']['id'] ?? 0;
$node = $panel->nodes()->get($nodeId);

// ─── Test node connectivity before saving ────────────────────────
$testResult = $panel->nodes()->test([
    'scheme'   => 'https',
    'address'  => 'remote.example.com',
    'port'     => 54321,
    'basePath' => '/',
    'apiToken' => 'test-token',
]);
echo "Connection test: " . ($testResult['success'] ? 'OK' : 'FAILED') . "\n";

// ─── Probe an existing node ──────────────────────────────────────
$probeResult = $panel->nodes()->probe($nodeId);
echo "Probe result: " . ($probeResult['success'] ? 'healthy' : 'unreachable') . "\n";

// ─── Update node ─────────────────────────────────────────────────
$panel->nodes()->update($nodeId, [
    'name'    => 'Germany Node (Updated)',
    'scheme'  => 'https',
    'address' => 'germany-new.example.com',
    'port'    => 54321,
    'basePath' => '/',
    'apiToken' => 'new-api-token',
]);

// ─── Toggle node enable/disable ──────────────────────────────────
$panel->nodes()->setEnable($nodeId, false);

// ─── Node metric history ─────────────────────────────────────────
$cpuHistory = $panel->nodes()->history($nodeId, 'cpu', '6h');
$memHistory = $panel->nodes()->history($nodeId, 'memory', '6h');
$netHistory = $panel->nodes()->history($nodeId, 'network', '24h');

foreach ($cpuHistory['obj'] ?? [] as $point) {
    echo "CPU at " . date('H:i', $point['t']) . ": {$point['v']}%\n";
}

// ─── Delete node ─────────────────────────────────────────────────
$panel->nodes()->delete($nodeId);
