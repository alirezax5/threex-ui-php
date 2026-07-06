<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// ─── Get all panel settings ──────────────────────────────────────
$settings = $panel->settings()->all();
echo "Web Port: " . ($settings['obj']['webPort'] ?? 'N/A') . "\n";
echo "Web Path: " . ($settings['obj']['webPath'] ?? 'N/A') . "\n";
echo "Language: " . ($settings['obj']['language'] ?? 'N/A') . "\n";

// ─── Get computed default settings ───────────────────────────────
$defaults = $panel->settings()->defaultSettings();

// ─── Update panel settings ───────────────────────────────────────
$panel->settings()->update([
    'webPort'     => 2053,
    'webPath'     => '/',
    'language'    => 'en-US',
    'timeZone'    => 'UTC',
    'expireDiff'  => 0,
    'trafficDiff' => 0,
]);

// ─── Change admin credentials ────────────────────────────────────
$panel->settings()->updateUser(
    oldUsername: 'admin',
    oldPassword: 'current-password',
    newUsername: 'newadmin',
    newPassword: 'new-strong-password'
);

// ─── Restart panel ───────────────────────────────────────────────
echo "Restarting panel...\n";
// $panel->settings()->restartPanel();

// ─── Get default Xray JSON config ────────────────────────────────
$defaultConfig = $panel->settings()->getDefaultJsonConfig();
echo "Default Xray config:\n" . $defaultConfig['obj'] . "\n";

// ─── API Token Management ────────────────────────────────────────
// List all tokens
$tokens = $panel->settings()->getApiTokens();
foreach ($tokens['obj'] ?? [] as $token) {
    echo "Token #{$token['id']}: {$token['name']} (enabled: " . ($token['enabled'] ? 'yes' : 'no') . ")\n";
}

// Create a new token
$newToken = $panel->settings()->createApiToken('readonly-token');
echo "New token: " . ($newToken['obj']['token'] ?? '') . "\n";

// Toggle token enabled/disabled
$tokenId = $newToken['obj']['id'] ?? 0;
$panel->settings()->setApiTokenEnabled($tokenId, false);

// Delete a token
$panel->settings()->deleteApiToken($tokenId);

echo "Settings management complete.\n";
