# 3X-UI PHP Client — مستندات کامل

> **زبان:** [English](DOCUMENTATION.md) | [فارسی](#)

<div dir="rtl">

## فهرست مطالب

1. [نصب و راه‌اندازی](#نصب-و-راه‌اندازی)
2. [احراز هویت](#احراز-هویت)
3. [مرجع API](#مرجع-api)
   - [Inbounds (اینباندها)](#inbounds)
   - [Clients (کلاینت‌ها)](#clients)
   - [Client Groups (گروه کلاینت‌ها)](#client-groups)
   - [Server (سرور)](#server)
   - [Nodes (نودها)](#nodes)
   - [Settings (تنظیمات)](#settings)
   - [XrayConfig](#xrayconfig)
   - [CustomGeo](#customgeo)
   - [Subscriptions (سابسکریپشن‌ها)](#subscriptions)
4. [Retry خودکار](#retry-خودکار)
5. [توابع کمکی](#توابع-کمکی)
6. [مدیریت خطا](#مدیریت-خطا)
7. [پیکربندی پیشرفته](#پیکربندی-پیشرفته)

---

## نصب و راه‌اندازی

```bash
composer require alirezax5/threex-ui-php
```

### نیازمندی‌ها
- PHP >= 8.1
- ext-curl
- ext-json
- ext-mbstring

### شروع سریع

```php
require_once 'vendor/autoload.php';

use ThreeXUI\ThreeXUI;

// حالت API Token
$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->withApiToken('your-api-token');

// یا ورود Session
$panel = new ThreeXUI('https://panel.example.com:54321');
$panel->login('admin', 'password');
```

---

## احراز هویت

پنل 3X-UI از دو روش احراز هویت پشتیبانی می‌کند:

### 1. API Token (Bearer) — توصیه می‌شود
توکن را از مسیر **تنظیمات پنل → امنیت → API Token** دریافت کنید.

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->withApiToken('token-here');
```

### 2. Session Login (مبتنی بر کوکی)

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->login('username', 'password');

// بررسی وضعیت 2FA
if ($panel->isTwoFactorEnabled()) {
    // مدیریت 2FA
}

$panel->logout();
```

---

## مرجع API

همه اندپوینت‌ها envelope استاندارد 3X-UI را برمی‌گردانند:

```json
{
    "success": true|false,
    "msg": "پیام",
    "obj": <داده>
}
```

### Inbounds

دسترسی: `$panel->inbounds()`

| متد | پارامترها | توضیح |
|-----|-----------|--------|
| `list()` | — | لیست کامل اینباندها با آمار کلاینت |
| `listSlim()` | — | لیست خلاصه (فقط ایمیل + فعال/غیرفعال) |
| `options()` | — | گزینشگر: id, remark, protocol, port |
| `get(int $id)` | شناسه اینباند | دریافت یک اینباند |
| `add(array $data)` | تنظیمات کامل | ایجاد اینباند |
| `update(int $id, array $data)` | شناسه + تنظیمات | بروزرسانی کامل |
| `delete(int $id)` | شناسه | حذف اینباند |
| `setEnable(int $id, bool $enable)` | شناسه + وضعیت | تغییر وضعیت فعال/غیرفعال |
| `resetTraffic(int $id)` | شناسه | ریست شمارنده‌ها |
| `delAllClients(int $id)` | شناسه | حذف همه کلاینت‌ها |
| `getFallbacks(int $id)` | شناسه | لیست قوانین fallback |
| `setFallbacks(int $id, array $fallbacks)` | شناسه + قوانین | جایگزینی fallback‌ها |
| `resetAllTraffics()` | — | ریست ترافیک همه اینباندها |
| `import(string $jsonData)` | رشته JSON | ایمپورت گروهی |

### Clients

دسترسی: `$panel->clients()`

| متد | توضیح |
|-----|--------|
| `listPaged(array $params)` | صفحه‌بندی با جستجو/فیلتر |
| `list()` | لیست کامل با ترافیک |
| `get(string $email)` | یک کلاینت |
| `add(array $clientData, array $inboundIds)` | ایجاد + اتصال |
| `update(string $email, array $data)` | بروزرسانی کامل |
| `delete(string $email, bool $keepTraffic)` | حذف کلاینت |
| `attach(string $email, array $inboundIds)` | اتصال به اینباندها |
| `detach(string $email, array $inboundIds)` | قطع اتصال |
| `resetTraffic(string $email)` | ریست یک کلاینت |
| `resetAllTraffics()` | ریست همه |
| `updateTraffic(string $email, int $up, int $down)` | تنظیم شمارنده‌ها |
| `ips(string $email)` | تاریخچه IP مبدا |
| `clearIps(string $email)` | پاک کردن تاریخچه IP |
| `onlines()` | ایمیل کلاینت‌های آنلاین |
| `lastOnline()` | آخرین زمان حضور |
| `traffic(string $email)` | شمارنده‌های ترافیک |
| `subLinks(string $subId)` | لینک‌های سابسکریپشن |
| `links(string $email)` | URLهای پروتکل هر کلاینت |
| `delDepleted()` | حذف کلاینت‌های منقضی/اتمام حجم |
| **عملیات گروهی** | |
| `bulkAdjust(array $emails, ?int $addDays, ?int $addBytes)` | تغییر انقضا/حجم |
| `bulkDelete(array $emails, bool $keepTraffic)` | حذف چندتایی |
| `bulkCreate(array $entries)` | ایجاد چندتایی |
| `bulkAttach(array $emails, array $inboundIds)` | اتصال چندتایی |
| `bulkDetach(array $emails, array $inboundIds)` | قطع اتصال چندتایی |
| `bulkResetTraffic(array $emails)` | ریست چندتایی |

### Client Groups

دسترسی: `$panel->clientGroups()`

| متد | توضیح |
|-----|--------|
| `list()` | همه گروه‌ها با تعداد اعضا |
| `create(string $name)` | ایجاد گروه |
| `rename(string $old, string $new)` | تغییر نام |
| `delete(string $name)` | حذف گروه |
| `bulkAdd(array $emails, string $group)` | افزودن به گروه |
| `bulkRemove(array $emails)` | حذف از گروه |
| `emails(string $name)` | دریافت اعضا |

### Server

دسترسی: `$panel->server()`

| متد | توضیح |
|-----|--------|
| `status()` | CPU, RAM, Disk, Net, وضعیت Xray |
| `cpuHistory(string $bucket)` | سری زمانی CPU |
| `history(string $metric, string $bucket)` | سری زمانی عمومی |
| `xrayMetricsState()` | وضعیت متریک‌های Xray |
| `xrayMetricsHistory(string $metric, string $bucket)` | سری زمانی Xray |
| `xrayObservatory()` | سلامت outbound‌ها |
| `xrayObservatoryHistory(string $tag, string $bucket)` | داده هر تگ |
| `getXrayVersion()` | نسخه‌های موجود |
| `getPanelUpdateInfo()` | بررسی بروزرسانی |
| `getConfigJson()` | پیکربندی در حال اجرای Xray |
| `getDb()` | بکاپ SQLite |
| `getNewUUID()` | تولید UUID v4 |
| `getNewX25519Cert()` | کلید Reality |
| `getNewMldsa65()` | کلید ML-DSA-65 |
| `getNewMlkem768()` | کلید ML-KEM-768 |
| `getNewVlessEnc()` | گزینه‌های رمزنگاری VLESS |
| `getNewEchCert(string $sni)` | کلید ECH |
| `stopXrayService()` | توقف Xray |
| `restartXrayService()` | راه‌اندازی مجدد Xray |
| `installXray(string $version)` | نصب نسخه |
| `updatePanel()` | بروزرسانی خودکار |
| `updateGeofile(string $fileName)` | بروزرسانی یک فایل geo |
| `updateAllGeofiles()` | بروزرسانی همه فایل‌های geo |
| `logs(int $count, ?string $level, bool $syslog)` | لاگ‌های پنل |
| `xrayLogs(int $count)` | لاگ‌های Xray |
| `importDb(string $filePath)` | بازیابی SQLite |

### Nodes

دسترسی: `$panel->nodes()`

| متد | توضیح |
|-----|--------|
| `list()` | همه نودها |
| `get(int $id)` | دریافت نود |
| `add(array $data)` | افزودن نود |
| `update(int $id, array $data)` | بروزرسانی نود |
| `delete(int $id)` | حذف نود |
| `setEnable(int $id, bool $enable)` | تغییر وضعیت |
| `test(array $connectionData)` | تست اتصال (بدون ذخیره) |
| `probe(int $id)` | تست نود موجود |
| `history(int $id, string $metric, string $bucket)` | متریک‌ها |

### Settings

دسترسی: `$panel->settings()`

| متد | توضیح |
|-----|--------|
| `all()` | دریافت همه تنظیمات |
| `defaultSettings()` | مقادیر پیش‌فرض |
| `update(array $settings)` | ذخیره تنظیمات |
| `updateUser(...)` | تغییر نام کاربری/رمز |
| `restartPanel()` | راه‌اندازی مجدد پنل |
| `getDefaultJsonConfig()` | قالب پیش‌فرض Xray |
| `getApiTokens()` / `createApiToken($name)` / `deleteApiToken($id)` | مدیریت توکن‌ها |

### XrayConfig

دسترسی: `$panel->xrayConfig()`

| متد | توضیح |
|-----|--------|
| `getConfig()` | قالب + تگ‌ها |
| `getOutboundsTraffic()` | آمار outbound |
| `warp(string $action)` | مدیریت Warp |
| `nord(string $action)` | مدیریت NordVPN |
| `resetOutboundsTraffic(?string $tag)` | ریست بر اساس تگ |
| `testOutbound(array $config)` | تست پیکربندی |

### Subscriptions

دسترسی: `$panel->subscriptions()`

| متد | توضیح |
|-----|--------|
| `getBase64(string $subId)` | سابسکریپشن Base64 |
| `getJson(string $subId)` | سابسکریپشن JSON |
| `getClash(string $subId)` | سابسکریپشن Clash YAML |
| `backupToTelegram()` | ارسال بکاپ به تلگرام |

---

## Retry خودکار

برای خطاهای موقت (Connection قطع شده، 5xx سرور، Rate Limit) به صورت خودکار تلاش مجدد می‌کند:

```php
$panel->withRetry(
    maxAttempts: 3,    // حداکثر تعداد تلاش (پیش‌فرض: 3)
    baseDelayMs: 500,  // تأخیر پایه به میلی‌ثانیه (پیش‌فرض: 500)
    multiplier: 2.0,   // ضریب نمایی (پیش‌فرض: 2.0)
    maxDelayMs: 10000  // سقف تأخیر به میلی‌ثانیه (پیش‌فرض: 10000)
);
```

### رفتار Retry:
- ✅ **تلاش مجدد روی:** ConnectionException، کدهای 408, 429, 500, 502, 503, 504
- ❌ **عدم تلاش مجدد روی:** AuthenticationException، خطاهای 4xx
- از exponential backoff با jitter تصادفی استفاده می‌کند

---

## توابع کمکی

توابع global موجود پس از include پکیج:

| تابع | توضیح |
|------|--------|
| `threexui_client(string $url, ?string $token)` | ایجاد سریع کلاینت |
| `bytes_to_human(int $bytes, int $precision)` | مثال: "1.5 GB" |
| `human_to_bytes(string $value)` | مثال: 1610612736 |
| `gb_to_bytes(float $gb)` | گیگابایت → بایت |
| `bytes_to_gb(int $bytes)` | بایت → گیگابایت |
| `array_dot_get(array $arr, string $key, $default)` | دسترسی با نقطه |
| `validate_uuid(string $value)` | بررسی فرمت UUID |
| `validate_protocol(string $protocol)` | بررسی پروتکل |
| `validate_port(mixed $port)` | بررسی محدوده پورت |

---

## مدیریت خطا

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
    // فیلدهای نامعتبر یا مفقود
} catch (AuthenticationException $e) {
    // خطای ورود یا توکن
} catch (ApiException $e) {
    // خطای API (4xx/5xx)
    $apiData = $e->getResponseData();
} catch (ConnectionException $e) {
    // خطای شبکه/cURL
} catch (ThreeXUIException $e) {
    // هر خطای پکیج
    $context = $e->getContext();
}
```

---

## پیکربندی پیشرفته

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->withApiToken('token')
      ->setTimeout(60)               // تایم‌اوت ۶۰ ثانیه
      ->withoutSslVerification()     // رد شدن از SSL (فقط توسعه)
      ->withRetry(5, 1000, 2.0, 30000);  // ۵ تلاش با تأخیر نمایی
```

</div>
