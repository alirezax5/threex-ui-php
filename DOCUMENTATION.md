# 3X-UI PHP Client — Full Documentation

> **Language:** [English](#) | [فارسی](DOCUMENTATION.fa.md)

## Table of Contents

1. [Installation & Setup](#installation--setup)
2. [Authentication](#authentication)
3. [API Reference](#api-reference)
   - [Inbounds](#inbounds)
   - [Clients](#clients)
   - [Client Groups](#client-groups)
   - [Server](#server)
   - [Nodes](#nodes)
   - [Settings](#settings)
   - [XrayConfig](#xrayconfig)
   - [CustomGeo](#customgeo)
   - [Subscriptions](#subscriptions)
4. [Helper Functions](#helper-functions)
5. [Exception Handling](#exception-handling)
6. [Advanced Configuration](#advanced-configuration)
7. [Development Phases](#development-phases)

---

## Installation & Setup

```bash
composer require alirezax5/threex-ui-php
```

### Requirements
- PHP >= 8.1
- ext-curl
- ext-json
- ext-mbstring

### Quick Start

```php
require_once 'vendor/autoload.php';

use ThreeXUI\ThreeXUI;

// API Token mode
$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// Or session login
$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->login('admin', 'password');
```

---

## Authentication

The 3X-UI panel supports two authentication methods:

### 1. API Token (Bearer) — Recommended
Get your token from: **Panel Settings → Security → API Token**

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->withApiToken('token-here');
```

### 2. Session Login (Cookie-based)

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->login('username', 'password');

// Check 2FA status
if ($panel->isTwoFactorEnabled()) {
    // Handle 2FA
}

$panel->logout();
```

---

## API Reference

All endpoints return the standard 3X-UI envelope:

```json
{
    "success": true|false,
    "msg": "message",
    "obj": <payload>
}
```

### Inbounds

Access: `$panel->inbounds()`

| Method | Parameters | Description |
|--------|-----------|-------------|
| `list()` | — | Full inbound list with client stats |
| `listSlim()` | — | Lightweight list (email + enable only) |
| `options()` | — | Picker: id, remark, protocol, port |
| `get(int $id)` | Inbound ID | Fetch single inbound |
| `add(array $data)` | Full inbound config | Create inbound |
| `update(int $id, array $data)` | ID + config | Full-replace update |
| `delete(int $id)` | Inbound ID | Delete inbound |
| `setEnable(int $id, bool $enable)` | ID + state | Toggle enable |
| `resetTraffic(int $id)` | Inbound ID | Reset counters |
| `delAllClients(int $id)` | Inbound ID | Remove all clients |
| `getFallbacks(int $id)` | Inbound ID | List fallback rules |
| `setFallbacks(int $id, array $fallbacks)` | ID + rules | Replace fallbacks |
| `resetAllTraffics()` | — | Reset all inbound traffic |
| `import(string $jsonData)` | JSON string | Bulk import |

### Clients

Access: `$panel->clients()`

| Method | Description |
|--------|-------------|
| `listPaged(array $params)` | Paginated with search/filter |
| `list()` | Full list with traffic |
| `get(string $email)` | Single client |
| `add(array $clientData, array $inboundIds)` | Create + attach |
| `update(string $email, array $data)` | Full-replace update |
| `delete(string $email, bool $keepTraffic)` | Delete client |
| `attach(string $email, array $inboundIds)` | Attach to inbounds |
| `detach(string $email, array $inboundIds)` | Detach |
| `resetTraffic(string $email)` | Reset single client |
| `resetAllTraffics()` | Reset all |
| `updateTraffic(string $email, int $up, int $down)` | Set counters |
| `ips(string $email)` | Source IP history |
| `clearIps(string $email)` | Clear IP history |
| `onlines()` | Online client emails |
| `lastOnline()` | Last-seen timestamps |
| `traffic(string $email)` | Traffic counters |
| `subLinks(string $subId)` | Subscription links |
| `links(string $email)` | Per-client protocol URLs |
| `delDepleted()` | Remove expired/quota-exhausted |
| **Bulk Operations** | |
| `bulkAdjust(array $emails, ?int $addDays, ?int $addBytes)` | Shift expiry/quota |
| `bulkDelete(array $emails, bool $keepTraffic)` | Delete many |
| `bulkCreate(array $entries)` | Create many |
| `bulkAttach(array $emails, array $inboundIds)` | Attach many |
| `bulkDetach(array $emails, array $inboundIds)` | Detach many |
| `bulkResetTraffic(array $emails)` | Reset many |

### Client Groups

Access: `$panel->clientGroups()`

| Method | Description |
|--------|-------------|
| `list()` | All groups with counts |
| `create(string $name)` | Create group |
| `rename(string $old, string $new)` | Rename |
| `delete(string $name)` | Delete group |
| `bulkAdd(array $emails, string $group)` | Add to group |
| `bulkRemove(array $emails)` | Remove from group |
| `emails(string $name)` | Get members |

### Server

Access: `$panel->server()`

| Method | Description |
|--------|-------------|
| `status()` | CPU, RAM, Disk, Net, Xray state |
| `cpuHistory(string $bucket)` | CPU time-series |
| `history(string $metric, string $bucket)` | Generic time-series |
| `xrayMetricsState()` | Xray metrics state |
| `xrayMetricsHistory(string $metric, string $bucket)` | Xray time-series |
| `xrayObservatory()` | Outbound health |
| `xrayObservatoryHistory(string $tag, string $bucket)` | Per-tag data |
| `getXrayVersion()` | Available versions |
| `getPanelUpdateInfo()` | Update check |
| `getConfigJson()` | Running Xray config |
| `getDb()` | SQLite backup stream |
| `getNewUUID()` | Generate UUID v4 |
| `getNewX25519Cert()` | Reality keypair |
| `getNewMldsa65()` | ML-DSA-65 keypair |
| `getNewMlkem768()` | ML-KEM-768 keypair |
| `getNewVlessEnc()` | VLESS encryption options |
| `getNewEchCert(string $sni)` | ECH keypair |
| `stopXrayService()` | Stop Xray |
| `restartXrayService()` | Restart Xray |
| `installXray(string $version)` | Install version |
| `updatePanel()` | Self-update |
| `updateGeofile(string $fileName)` | Refresh one geo file |
| `updateAllGeofiles()` | Refresh all geo files |
| `logs(int $count, ?string $level, bool $syslog)` | Panel logs |
| `xrayLogs(int $count)` | Xray logs |
| `importDb(string $filePath)` | Restore SQLite DB |

### Nodes

Access: `$panel->nodes()`

| Method | Description |
|--------|-------------|
| `list()` | All nodes |
| `get(int $id)` | Fetch node |
| `add(array $data)` | Add node |
| `update(int $id, array $data)` | Update node |
| `delete(int $id)` | Delete node |
| `setEnable(int $id, bool $enable)` | Toggle |
| `test(array $connectionData)` | Probe (no save) |
| `probe(int $id)` | Probe existing |
| `history(int $id, string $metric, string $bucket)` | Metrics |

### Settings

Access: `$panel->settings()`

| Method | Description |
|--------|-------------|
| `all()` | Get all settings |
| `defaultSettings()` | Computed defaults |
| `update(array $settings)` | Save settings |
| `updateUser(string $oldUser, string $oldPass, string $newUser, string $newPass)` | Change creds |
| `restartPanel()` | Restart panel |
| `getDefaultJsonConfig()` | Default Xray JSON |
| `getApiTokens()` | List tokens |
| `createApiToken(string $name)` | Create token |
| `deleteApiToken(int $id)` | Delete token |
| `setApiTokenEnabled(int $id, bool $enabled)` | Toggle token |

### XrayConfig

Access: `$panel->xrayConfig()`

| Method | Description |
|--------|-------------|
| `getConfig()` | Template + tags |
| `getDefaultJsonConfig()` | Default template |
| `getOutboundsTraffic()` | Outbound stats |
| `getXrayResult()` | stdout/stderr |
| `update(array $fields)` | Save config |
| `warp(string $action)` | Warp management |
| `nord(string $action)` | NordVPN management |
| `resetOutboundsTraffic(?string $tag)` | Reset by tag |
| `testOutbound(array $config)` | Test config |

### CustomGeo

Access: `$panel->customGeo()`

| Method | Description |
|--------|-------------|
| `list()` | List sources |
| `aliases()` | List aliases |
| `add(string $type, string $alias, string $url)` | Add source |
| `update(int $id, array $data)` | Update source |
| `delete(int $id)` | Delete source |
| `download(int $id)` | Re-download one |
| `updateAll()` | Re-download all |

### Subscriptions

Access: `$panel->subscriptions()`

| Method | Description |
|--------|-------------|
| `getBase64(string $subId, string $path)` | Base64 subscription |
| `getJson(string $subId, string $path)` | JSON subscription |
| `getClash(string $subId, string $path)` | Clash YAML |
| `backupToTelegram()` | Send DB to Telegram bot |

---

## Helper Functions

Global functions available after including the package:

| Function | Description |
|----------|-------------|
| `threexui_client(string $url, ?string $token)` | Quick client factory |
| `bytes_to_human(int $bytes, int $precision)` | e.g. "1.5 GB" |
| `human_to_bytes(string $value)` | e.g. 1610612736 |
| `gb_to_bytes(float $gb)` | GB → bytes |
| `bytes_to_gb(int $bytes)` | bytes → GB |
| `array_dot_get(array $arr, string $key, $default)` | Dot notation access |
| `validate_uuid(string $value)` | UUID format check |
| `validate_protocol(string $protocol)` | Protocol check |
| `validate_port(mixed $port)` | Port range check |

---

## Exception Handling

```php
use ThreeXUI\Exceptions\{
    ThreeXUIException,
    AuthenticationException,
    ApiException,
    ConnectionException,
    ValidationException
};

try {
    $response = $panel->inbounds()->add([...]);
} catch (ValidationException $e) {
    // Missing or invalid fields
} catch (AuthenticationException $e) {
    // Login or token failure
} catch (ApiException $e) {
    // API returned error (4xx/5xx)
    $apiData = $e->getResponseData();
} catch (ConnectionException $e) {
    // Network/cURL error
} catch (ThreeXUIException $e) {
    // Any package exception
    $context = $e->getContext();
}
```

---

## Advanced Configuration

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->withApiToken('token')
      ->setTimeout(60)              // 60s timeout
      ->withoutSslVerification();   // Skip SSL (dev only)
```

---

## Development Phases

### Phase 1 — Foundation ✓
- [x] Project structure & composer setup
- [x] Config & HTTP client core
- [x] Authentication (session + token)
- [x] Exception hierarchy
- [x] Helper classes (Validator, Formatter, ArrayHelper)

### Phase 2 — API Coverage ✓
- [x] Inbounds endpoints
- [x] Clients endpoints (incl. bulk ops)
- [x] Client Groups endpoints
- [x] Server endpoints
- [x] Nodes endpoints
- [x] Settings endpoints
- [x] XrayConfig endpoints
- [x] CustomGeo endpoints
- [x] Subscriptions endpoints

### Phase 3 — Polish
- [ ] Unit tests with PHPUnit
- [ ] PHPStan static analysis (level max)
- [ ] GitHub Actions CI/CD
- [ ] Rate limiting support
- [ ] Retry logic for transient errors
- [ ] Response caching layer (optional)
- [ ] Laravel service provider
- [ ] Symfony bundle

### Phase 4 — Extended Features
- [ ] WebSocket support for real-time data
- [ ] Async HTTP with Guzzle promises
- [ ] Event system (before/after hooks)
- [ ] CLI tool for panel management
- [ ] Docker image for testing
