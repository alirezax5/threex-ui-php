# 3X-UI PHP Client

> **زبان:** [English](README.md) | [فارسی](#)

<div dir="rtl">

یک کلاینت **PHP 8.1+** مدرن، ماژولار و بهینه برای [پنل 3X-UI](https://github.com/MHSanaei/3x-ui) — مدیریت برنامه‌نویسی اینباندها، کلاینت‌ها، نودها، تنظیمات سرور و سابسکریپشن‌های Xray/V2Ray.

## ویژگی‌ها

- **پوشش کامل API** — حدود 96 اندپوینت در 9 گروه عملیاتی
- **احراز هویت دوگانه** — API Token (Bearer) یا ورود مبتنی بر Session
- **معماری ماژولار** — هر گروه API یک کلاس مجزا و قابل تست است
- **Type-Safe** — PHP 8.1+ با strict types، readonly properties و type-hinting کامل
- **PSR-4** — بارگذاری خودکار Composer با ساختار namespace تمیز
- **بدون وابستگی** — فقط از ext-curl و ext-json استفاده می‌کند
- **توابع کمکی** — توابع global برای کارهای رایج
- **مدیریت خطای جامع** — استثناهای سفارشی برای خطاهای احراز هویت، API، اتصال و اعتبارسنجی
- **اعتبارسنجی ورودی** — بررسی داخلی ایمیل، UUID، پورت، پروتکل قبل از ارسال
- **Retry خودکار** — تلاش مجدد با Exponential Backoff برای خطاهای موقت
- **تست‌های کامل** — بیش از ۱۴۴ تست با PHPUnit

## نیازمندی‌ها

| نیازمندی     | نسخه          |
|-------------|---------------|
| PHP         | >= 8.1        |
| ext-curl    | * (ضروری)     |
| ext-json    | * (ضروری)     |
| ext-mbstring| * (ضروری)     |

## نصب

```bash
composer require alirezax5/threex-ui-php
```

یا کلون مستقیم:

```bash
git clone https://github.com/alirezax5/threex-ui-php.git
cd threex-ui-php
composer install
```

## شروع سریع

### احراز هویت با API Token (توصیه می‌شود)

توکن را از مسیر **Settings → Security → API Token** دریافت کنید.

```php
use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://your-panel.example.com:54321');
$panel->withApiToken('your-api-token-from-settings');

$inbounds = $panel->inbounds()->list();
```

### احراز هویت با Session Login

```php
use ThreeXUI\ThreeXUI;

$panel = new ThreeXUI('https://your-panel.example.com:54321');
$panel->login('admin', 'your-password');

$status = $panel->server()->status();
```

### فعال‌سازی Retry خودکار

```php
$panel = new ThreeXUI('https://panel:54321');
$panel->withApiToken('token')
      ->withRetry(maxAttempts: 3, baseDelayMs: 500);
// تمام درخواست‌ها حالا با retry خودکار اجرا می‌شوند
```

## گروه‌های API

| گروه           | دسترسی                    | توضیح                         |
|----------------|--------------------------|-------------------------------|
| Inbounds       | `$panel->inbounds()`     | مدیریت اینباندها               |
| Clients        | `$panel->clients()`      | مدیریت کلاینت‌ها + عملیات گروهی |
| Client Groups  | `$panel->clientGroups()` | گروه‌بندی کلاینت‌ها            |
| Server         | `$panel->server()`       | وضعیت سرور، کنترل Xray، لاگ‌ها |
| Nodes          | `$panel->nodes()`        | مدیریت کلاستر چند نودی          |
| Settings       | `$panel->settings()`     | تنظیمات پنل + مدیریت توکن API  |
| Xray Config    | `$panel->xrayConfig()`   | پیکربندی هسته Xray             |
| Custom Geo     | `$panel->customGeo()`    | منابع geoip/geosite سفارشی     |
| Subscriptions  | `$panel->subscriptions()`| لینک‌های سابسکریپشن + بکاپ تلگرام|

## مثال‌ها

```php
// افزودن اینباند VLESS
$panel->inbounds()->add([
    'remark'   => 'VLESS + Reality',
    'port'     => 443,
    'protocol' => 'vless',
    'settings' => json_encode(['clients' => [], 'decryption' => 'none', 'fallbacks' => []]),
    'streamSettings' => json_encode(['network' => 'tcp', 'security' => 'reality']),
    'sniffing' => json_encode(['enabled' => true, 'destOverride' => ['http', 'tls']]),
]);

// افزودن کلاینت
$panel->clients()->add(
    ['email' => 'user@example.com', 'totalGB' => 100],
    [1]
);

// وضعیت سرور
$status = $panel->server()->status();
// CPU, RAM, Disk, وضعیت Xray...

// ساخت کلید Reality
$keys = $panel->server()->getNewX25519Cert();

// ریست ترافیک همه کلاینت‌ها
$panel->clients()->resetAllTraffics();
```

## توابع کمکی

```php
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

## ساختار پروژه

```
threex-ui-php/
├── src/
│   ├── ThreeXUI.php              # Facade اصلی
│   ├── Config.php                # پیکربندی
│   ├── HttpClient.php            # کلاینت HTTP با cURL + retry
│   ├── Authentication.php        # ورود/خروج/2FA
│   ├── Contracts/                # اینترفیس‌ها
│   ├── Endpoints/                # 9 کلاس endpoint
│   ├── Exceptions/               # 5 کلاس استثنا
│   └── Helpers/                  # Validator, Formatter, ArrayHelper, RetryHandler, functions
├── tests/                        # 144 تست PHPUnit
├── examples/                     # 7 فایل نمونه
├── composer.json
├── README.md                     # مستندات انگلیسی
├── README.fa.md                  # مستندات فارسی
├── DOCUMENTATION.md              # مستندات کامل انگلیسی
├── DOCUMENTATION.fa.md           # مستندات کامل فارسی
├── CHANGELOG.txt
├── llm.txt                       # زمینه فشرده برای LLM
└── llm-full.txt                  # زمینه کامل برای LLM
```

## مدیریت خطا

```php
use ThreeXUI\Exceptions\AuthenticationException;
use ThreeXUI\Exceptions\ApiException;
use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ValidationException;

try {
    $panel->login('admin', 'wrong-password');
} catch (AuthenticationException $e) {
    echo "خطای احراز هویت: " . $e->getMessage();
} catch (ConnectionException $e) {
    echo "خطای شبکه: " . $e->getMessage();
} catch (ApiException $e) {
    echo "خطای API: " . $e->getMessage();
    $responseData = $e->getResponseData();
} catch (ValidationException $e) {
    echo "خطای اعتبارسنجی: " . $e->getMessage();
}
```

## اجرای تست‌ها

```bash
composer test
# Tests: 144, Assertions: 226
```

## لایسنس

MIT License. مشاهده [LICENSE](LICENSE).

## منابع مرتبط

- [پنل 3X-UI](https://github.com/MHSanaei/3x-ui) — پنل Xray که این کلاینت به آن متصل می‌شود
- [مستندات API](https://documenter.getpostman.com/view/5146551/2sBXwnsBko) — مستندات رسمی Postman

</div>
