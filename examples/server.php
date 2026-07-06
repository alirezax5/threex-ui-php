<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// ─── Server Status ───────────────────────────────────────────────
$status = $panel->server()->status();
$cpu = $status['obj']['cpu'] ?? 0;
$mem = $status['obj']['mem']['current'] ?? 0;
$disk = $status['obj']['disk']['current'] ?? 0;
$xrayState = $status['obj']['xray']['state'] ?? 'unknown';

echo "CPU: {$cpu}% | RAM: {$mem} | Disk: {$disk} | Xray: {$xrayState}\n";

// ─── CPU History (last hour) ─────────────────────────────────────
$cpuHistory = $panel->server()->cpuHistory('1h');
foreach ($cpuHistory['obj'] ?? [] as $point) {
    echo date('H:i:s', $point['t']) . " => {$point['v']}%\n";
}

// ─── Custom metric history ───────────────────────────────────────
$memHistory = $panel->server()->history('memory', '6h');
$netHistory = $panel->server()->history('network', '24h');

// ─── Xray Metrics ────────────────────────────────────────────────
$metricsState = $panel->server()->xrayMetricsState();
$xrayUplink = $panel->server()->xrayMetricsHistory('uplink', '1h');
$xrayDownlink = $panel->server()->xrayMetricsHistory('downlink', '1h');

// ─── Xray Observatory (outbound health) ──────────────────────────
$observatory = $panel->server()->xrayObservatory();
foreach ($observatory['obj'] ?? [] as $tag => $data) {
    $healthy = $data['healthy'] ? 'OK' : 'FAIL';
    echo "{$tag}: {$healthy} (latency: {$data['latency']}ms)\n";
}

// ─── Observatory per-tag history ─────────────────────────────────
$tagHistory = $panel->server()->xrayObservatoryHistory('proxy', '30m');

// ─── Generate Keys ───────────────────────────────────────────────
$uuid = $panel->server()->getNewUUID();
echo "New UUID: " . $uuid['obj'] . "\n";

$realityKeys = $panel->server()->getNewX25519Cert();
echo "Private: " . $realityKeys['obj']['privateKey'] . "\n";
echo "Public:  " . $realityKeys['obj']['publicKey'] . "\n";

$vlessEnc = $panel->server()->getNewVlessEnc();

// ─── Xray Service Control ────────────────────────────────────────
$installed = $panel->server()->getXrayVersion();
echo "Available Xray versions: " . implode(', ', $installed['obj'] ?? []) . "\n";

$panel->server()->restartXrayService();
sleep(2);
$panel->server()->stopXrayService();

// ─── Install/Update Xray ─────────────────────────────────────────
$panel->server()->installXray('latest');

// ─── Update Panel ────────────────────────────────────────────────
$updateInfo = $panel->server()->getPanelUpdateInfo();
if ($updateInfo['obj']['hasUpdate'] ?? false) {
    echo "New version available: " . $updateInfo['obj']['latestVersion'] . "\n";
    $panel->server()->updatePanel();
}

// ─── Geo Files ───────────────────────────────────────────────────
$panel->server()->updateGeofile('geoip.dat');
$panel->server()->updateGeofile('geosite.dat');
// or all at once:
$panel->server()->updateAllGeofiles();

// ─── Logs ────────────────────────────────────────────────────────
$panelLogs = $panel->server()->logs(50, level: 'warning');
foreach ($panelLogs['obj'] ?? [] as $line) {
    echo $line . "\n";
}

$xrayLogs = $panel->server()->xrayLogs(100);

// ─── System Logs with syslog ─────────────────────────────────────
$sysLogs = $panel->server()->logs(200, level: 'error', syslog: true);

// ─── Xray Config ─────────────────────────────────────────────────
$configJson = $panel->server()->getConfigJson();
echo "Running config:\n" . $configJson['obj'] . "\n";

// ─── Database Backup ─────────────────────────────────────────────
$db = $panel->server()->getDb();
// Save to file:
file_put_contents(__DIR__ . '/backup.db', $db['obj'] ?? '');

// ─── Import SQLite DB ────────────────────────────────────────────
// $panel->server()->importDb(__DIR__ . '/restore.db');

// ─── ECH Certificate ─────────────────────────────────────────────
$echCert = $panel->server()->getNewEchCert('example.com');
