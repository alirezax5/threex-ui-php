<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;

// ─── Method 1: API Token (recommended) ───────────────────────────
$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token-here');

// ─── Method 2: Session login ─────────────────────────────────────
// $panel = new ThreeXUI('https://panel.example.com:54321');
// $panel->login('admin', 'your-password');

// ─── Inbounds ────────────────────────────────────────────────────
$inbounds = $panel->inbounds()->list();
print_r($inbounds);

// ─── Clients ─────────────────────────────────────────────────────
$clients = $panel->clients()->list();
print_r($clients);

// ─── Server Status ───────────────────────────────────────────────
$status = $panel->server()->status();
print_r($status);
