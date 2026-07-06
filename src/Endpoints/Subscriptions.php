<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;

class Subscriptions implements EndpointInterface
{
    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    /**
     * Get the Base64-encoded subscription.
     */
    public function getBase64(string $subId, string $path = 'sub'): string
    {
        $response = $this->http->get("/{$path}/{$subId}");

        return $response['obj'] ?? '';
    }

    /**
     * Get the JSON subscription details.
     */
    public function getJson(string $subId, string $path = 'json'): array
    {
        return $this->http->get("/{$path}/{$subId}");
    }

    /**
     * Get the Clash YAML subscription.
     */
    public function getClash(string $subId, string $path = 'clash'): string
    {
        $response = $this->http->get("/{$path}/{$subId}");

        return $response['obj'] ?? '';
    }

    /**
     * Backup database to Telegram bot.
     */
    public function backupToTelegram(): array
    {
        return $this->http->post('/panel/api/backuptotgbot');
    }
}
