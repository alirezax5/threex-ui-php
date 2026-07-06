<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;
use ThreeXUI\Exceptions\AuthenticationException;
use ThreeXUI\Exceptions\ApiException;
use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ValidationException;

$panel = new ThreeXUI('https://panel.example.com:54321');

// ─── Session-based login with 2FA ────────────────────────────────
try {
    if ($panel->isTwoFactorEnabled()) {
        echo "2FA is enabled. Prompt user for code.\n";
        // In production, capture 2FA code from user input
        // $panel->login('admin', 'password', twoFactorCode: '123456');
    } else {
        $panel->login('admin', 'your-password');
    }

    echo "Logged in successfully.\n";
} catch (AuthenticationException $e) {
    echo "Login failed: " . $e->getMessage() . "\n";
    exit(1);
}

// ─── With retry + SSL skip (dev only) ────────────────────────────
$panel->withRetry(maxAttempts: 5, baseDelayMs: 1000, maxDelayMs: 30000)
      ->withoutSslVerification()
      ->setTimeout(60);

// ─── Comprehensive error handling ─────────────────────────────────
function safeApiCall(callable $fn, string $label): void
{
    try {
        $result = $fn();
        echo "[OK] {$label}: success={$result['success']}\n";
    } catch (ConnectionException $e) {
        echo "[NETWORK] {$label}: {$e->getMessage()} (retrying...)\n";
    } catch (ApiException $e) {
        echo "[API] {$label}: HTTP {$e->getCode()} - {$e->getMessage()}\n";
        $ctx = $e->getResponseData();
        if ($ctx) {
            echo "  Response: " . json_encode($ctx) . "\n";
        }
    } catch (ValidationException $e) {
        echo "[VALIDATION] {$label}: {$e->getMessage()}\n";
    } catch (\Throwable $e) {
        echo "[ERROR] {$label}: {$e->getMessage()}\n";
    }
}

// ─── Bulk migration: copy clients from one inbound to another ────
function migrateClients($panel, int $sourceInbound, int $targetInbound): void
{
    $inbound = $panel->inbounds()->get($sourceInbound);
    $clients = $inbound['obj']['clientStats'] ?? [];

    $emails = array_column($clients, 'email');
    echo "Migrating " . count($emails) . " clients from inbound {$sourceInbound} to {$targetInbound}\n";

    $panel->clients()->bulkAttach($emails, [$targetInbound]);
    echo "Migration complete.\n";
}

// ─── Subscription link management ────────────────────────────────
$subId = 'your-subscription-id';

// Base64 subscription (standard)
$base64Sub = $panel->subscriptions()->getBase64($subId);

// JSON subscription (for custom parsers)
$jsonSub = $panel->subscriptions()->getJson($subId);

// Clash Meta YAML subscription
$clashSub = $panel->subscriptions()->getClash($subId);

// Backup database to Telegram bot
$panel->subscriptions()->backupToTelegram();

// ─── Custom Geo sources ──────────────────────────────────────────
$sources = $panel->customGeo()->list();
echo "Custom geo sources:\n";
foreach ($sources['obj'] ?? [] as $source) {
    echo "  #{$source['id']} {$source['type']}:{$source['alias']} -> {$source['url']}\n";
}

$aliases = $panel->customGeo()->aliases();

$panel->customGeo()->add('geoip', 'my-company-ip', 'https://example.com/ips.dat');
$panel->customGeo()->download(1);
$panel->customGeo()->updateAll();

// ─── Scheduled maintenance script pattern ────────────────────────
function dailyMaintenance($panel): array
{
    $report = [];

    // 1. Health check
    $report['status'] = $panel->server()->status();

    // 2. Delete depleted (expired or exhausted quota) clients
    $report['delDepleted'] = $panel->clients()->delDepleted();

    // 3. Reset traffic for all inbounds (monthly rollover)
    $report['resetTraffic'] = $panel->inbounds()->resetAllTraffics();

    // 4. Update geo files
    $report['geoUpdate'] = $panel->server()->updateAllGeofiles();

    return $report;
}

// ─── Logout ──────────────────────────────────────────────────────
$panel->logout();
echo "Session ended.\n";
