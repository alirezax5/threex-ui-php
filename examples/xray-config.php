<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// ─── Get Xray config template ────────────────────────────────────
$config = $panel->xrayConfig()->getConfig();
$inboundTags = $config['obj']['inboundTags'] ?? [];
$reverseTags = $config['obj']['reverseTags'] ?? [];
echo "Inbound tags: " . implode(', ', $inboundTags) . "\n";

// ─── Get default JSON config ─────────────────────────────────────
$default = $panel->xrayConfig()->getDefaultJsonConfig();

// ─── Get outbound traffic stats ──────────────────────────────────
$traffic = $panel->xrayConfig()->getOutboundsTraffic();
foreach ($traffic['obj'] ?? [] as $tag => $data) {
    echo "{$tag}: up=" . bytes_to_human($data['up'] ?? 0)
       . " down=" . bytes_to_human($data['down'] ?? 0) . "\n";
}

// ─── Reset outbound traffic for a specific tag ───────────────────
$panel->xrayConfig()->resetOutboundsTraffic('proxy');

// Reset all outbound traffic
$panel->xrayConfig()->resetOutboundsTraffic();

// ─── Get Xray process output ─────────────────────────────────────
$xrayResult = $panel->xrayConfig()->getXrayResult();
echo "Xray output:\n" . ($xrayResult['obj'] ?? '') . "\n";

// ─── Update Xray config ──────────────────────────────────────────
$panel->xrayConfig()->update([
    'template' => 'advanced',
    'dns'      => json_encode(['servers' => ['8.8.8.8', '1.1.1.1']]),
]);

// ─── Cloudflare Warp management ──────────────────────────────────
$panel->xrayConfig()->warp('start');
$panel->xrayConfig()->warp('stop');
$panel->xrayConfig()->warp('restart');
$panel->xrayConfig()->warp('status');

// ─── NordVPN management ──────────────────────────────────────────
$panel->xrayConfig()->nord('login');
$panel->xrayConfig()->nord('connect');
$panel->xrayConfig()->nord('disconnect');
$panel->xrayConfig()->nord('status');

// ─── Test outbound config ────────────────────────────────────────
$testResult = $panel->xrayConfig()->testOutbound([
    'tag'      => 'test-proxy',
    'protocol' => 'freedom',
    'settings' => json_encode([]),
]);
echo "Outbound test latency: " . ($testResult['obj']['latency'] ?? 0) . "ms\n";

echo "Xray config management complete.\n";
