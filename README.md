# 3X-UI PHP Client

> **Language:** [English](#) | [فارسی](README.fa.md)

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)](composer.json)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-144%20passed-brightgreen)](tests/)

A modern, modular, and optimized **PHP 8.1+ client** for the [3X-UI Panel](https://github.com/MHSanaei/3x-ui) REST API — programmatically manage Xray/V2Ray inbounds, clients, nodes, server settings, and subscriptions.

## Features

- **Full API Coverage** — ~96 endpoints across 9 functional groups
- **Dual Authentication** — API token (Bearer) or session-based login
- **Modular Architecture** — Each API group is a separate, testable class
- **Type-Safe** — PHP 8.1+ with strict types, readonly properties, and full type-hinting
- **PSR-4** — Composer autoloading with clean namespace structure
- **Zero Dependencies** — Uses native `ext-curl` and `ext-json` only
- **Helper Functions** — Global convenience functions for common tasks
- **Comprehensive Error Handling** — Custom exceptions for auth, API, connection, and validation errors
- **Validator** — Built-in input validation for emails, UUIDs, ports, protocols
- **Formatter** — Bytes/Gigabytes conversion, timestamps, and sanitization

## Requirements

| Dependency   | Version        |
|-------------|----------------|
| PHP         | >= 8.1         |
| ext-curl    | * (required)   |
| ext-json    | * (required)   |
| ext-mbstring| * (required)   |

## Installation

```bash
composer require alirezax5/threex-ui-php
```

Or clone directly:

```bash
git clone https://github.com/alirezax5/threex-ui-php.git
cd threex-ui-php
composer install
```

## Quick Start

### Authentication via API Token (Recommended)

```php
use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://your-panel.example.com:54321');
$panel->withApiToken('your-api-token-from-settings');

$inbounds = $panel->inbounds()->list();
```

### Authentication via Session Login

```php
use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://your-panel.example.com:54321');
$panel->login('admin', 'your-password');

$status = $panel->server()->status();
```

## Usage Overview

| API Group      | Access Method               | Description                          |
|----------------|-----------------------------|--------------------------------------|
| Inbounds       | `$panel->inbounds()`        | CRUD inbound connections             |
| Clients        | `$panel->clients()`         | User/client management + bulk ops    |
| Client Groups  | `$panel->clientGroups()`    | Group clients together               |
| Server         | `$panel->server()`          | System status, Xray control, logs    |
| Nodes          | `$panel->nodes()`           | Multi-node cluster management        |
| Settings       | `$panel->settings()`        | Panel settings + API token mgmt      |
| Xray Config    | `$panel->xrayConfig()`      | Xray core configuration              |
| Custom Geo     | `$panel->customGeo()`       | Custom geoip/geosite sources         |
| Subscriptions  | `$panel->subscriptions()`   | Subscription links + Telegram backup |

## Examples

```php
// Add a VLESS inbound
$panel->inbounds()->add([
    'remark'   => 'VLESS + Reality',
    'port'     => 443,
    'protocol' => 'vless',
    'settings' => json_encode(['clients' => [], 'decryption' => 'none', 'fallbacks' => []]),
    'streamSettings' => json_encode(['network' => 'tcp', 'security' => 'reality']),
    'sniffing' => json_encode(['enabled' => true, 'destOverride' => ['http', 'tls']]),
]);

// Add a client
$panel->clients()->add(
    ['email' => 'user@example.com', 'totalGB' => 100],
    [1] // attach to inbound ID 1
);

// Get server status
$status = $panel->server()->status();
// CPU, RAM, Disk, Xray running state...

// Generate Reality keypair
$keys = $panel->server()->getNewX25519Cert();

// Reset all client traffic
$panel->clients()->resetAllTraffics();
```

## Helper Functions

```php
// Global convenience helpers
$client = threexui_client('https://panel:54321', 'token');

bytes_to_human(1073741824);          // "1 GB"
human_to_bytes('500 MB');            // 524288000
gb_to_bytes(10.5);                   // 11274289152
bytes_to_gb(10737418240);            // 10.0
validate_uuid('550e8400-e29b-...');  // true
validate_protocol('vless');          // true
validate_port(8080);                 // true
array_dot_get($data, 'settings.clients.0.email');
```

## Project Structure

```
threex-ui-php/
├── src/
│   ├── ThreeXUI.php              # Main facade
│   ├── Config.php                # Configuration
│   ├── HttpClient.php            # cURL HTTP client
│   ├── Authentication.php        # Login/logout/2FA
│   ├── Contracts/
│   │   ├── HttpClientInterface.php
│   │   └── EndpointInterface.php
│   ├── Endpoints/
│   │   ├── Inbounds.php
│   │   ├── Clients.php
│   │   ├── ClientGroups.php
│   │   ├── Server.php
│   │   ├── Nodes.php
│   │   ├── Settings.php
│   │   ├── XrayConfig.php
│   │   ├── CustomGeo.php
│   │   └── Subscriptions.php
│   ├── Exceptions/
│   │   ├── ThreeXUIException.php
│   │   ├── AuthenticationException.php
│   │   ├── ApiException.php
│   │   ├── ConnectionException.php
│   │   └── ValidationException.php
│   └── Helpers/
│       ├── Validator.php
│       ├── Formatter.php
│       ├── ArrayHelper.php
│       └── functions.php
├── examples/
│   ├── basic-usage.php
│   ├── inbounds.php
│   └── clients.php
├── composer.json
├── README.md
├── DOCUMENTATION.md
├── llm.txt
└── llm-full.txt
```

## Error Handling

```php
use ThreeXUI\Exceptions\AuthenticationException;
use ThreeXUI\Exceptions\ApiException;
use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ValidationException;

try {
    $panel->login('admin', 'wrong-password');
} catch (AuthenticationException $e) {
    echo "Auth failed: " . $e->getMessage();
} catch (ConnectionException $e) {
    echo "Network error: " . $e->getMessage();
} catch (ApiException $e) {
    echo "API error: " . $e->getMessage();
    $responseData = $e->getResponseData();
} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage();
}
```

## License

MIT License. See [LICENSE](LICENSE) for details.

## Related

- [3X-UI Panel](https://github.com/MHSanaei/3x-ui) — The Xray panel this client connects to
- [API Documentation](https://documenter.getpostman.com/view/5146551/2sBXwnsBko) — Official Postman docs
