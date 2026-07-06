<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ThreeXUI\ThreeXUI;
use ThreeXUI\Helpers\Formatter;

$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// ─── Utility helpers in action ───────────────────────────────────

// Format traffic sizes
$bytes = 2_500_000_000;
echo "Raw: {$bytes} bytes\n";
echo "Formatted: " . bytes_to_human($bytes) . "\n";
echo "In GB: " . bytes_to_gb($bytes) . " GB\n";
echo "500 GB in bytes: " . gb_to_bytes(500) . "\n";

// Parse user input
$userInput = '10 GB';
$quotaBytes = human_to_bytes($userInput);
echo "Quota: {$userInput} = {$quotaBytes} bytes\n";

// Dot notation array access
$response = [
    'success' => true,
    'obj' => [
        'settings' => [
            'clients' => [
                ['email' => 'user1@test.com', 'flow' => 'xtls-rprx-vision'],
                ['email' => 'user2@test.com', 'flow' => ''],
            ],
        ],
    ],
];

$firstClientEmail = array_dot_get($response, 'obj.settings.clients.0.email');
echo "First client email: {$firstClientEmail}\n";

// Validation helpers
$emails = ['valid@test.com', 'not-an-email', 'another@valid.io'];
$validEmails = array_filter($emails, '\\validate_uuid'); // wrong — email filter
$validEmails = array_filter($emails, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

$protocols = ['vless', 'vmess', 'ftp', 'trojan'];
$validProtocols = array_filter($protocols, '\\validate_protocol');
echo "Valid protocols: " . implode(', ', $validProtocols) . "\n";

$ports = [0, 80, 443, 65536, 8080];
$validPorts = array_filter($ports, '\\validate_port');
echo "Valid ports: " . implode(', ', $validPorts) . "\n";

// Formatter helpers
echo "Sanitized: " . Formatter::sanitize('<script>alert("xss")</script>') . "\n";
echo "Truncated: " . Formatter::truncate('This is a very long remark text that needs truncation', 30) . "\n";
echo "Expiry: " . Formatter::expiryTime(1_734_000_000_000) . "\n";

// ArrayHelper
$data = ['name' => 'Test', 'email' => null, 'extra' => '', 'config' => []];

$clean = \ThreeXUI\Helpers\ArrayHelper::removeEmpty($data);
echo "Cleaned data: " . json_encode($clean) . "\n";

$filtered = \ThreeXUI\Helpers\ArrayHelper::only(
    ['id' => 1, 'name' => 'Test', 'port' => 443, 'protocol' => 'vless'],
    ['name', 'protocol']
);
echo "Filtered: " . json_encode($filtered) . "\n";

$query = \ThreeXUI\Helpers\ArrayHelper::toQuery(['page' => 1, 'size' => 50]);
echo "Query string: {$query}\n";

echo "All helpers demonstrated.\n";
