<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;

class Settings implements EndpointInterface
{
    private const BASE = '/panel/setting';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function all(): array
    {
        return $this->http->post(self::BASE . '/all');
    }

    public function defaultSettings(): array
    {
        return $this->http->post(self::BASE . '/defaultSettings');
    }

    public function update(array $settings): array
    {
        return $this->http->post(self::BASE . '/update', $settings);
    }

    public function updateUser(string $oldUsername, string $oldPassword, string $newUsername, string $newPassword): array
    {
        return $this->http->post(self::BASE . '/updateUser', [
            'oldUsername' => $oldUsername,
            'oldPassword' => $oldPassword,
            'newUsername' => $newUsername,
            'newPassword' => $newPassword,
        ]);
    }

    public function restartPanel(): array
    {
        return $this->http->post(self::BASE . '/restartPanel');
    }

    public function getDefaultJsonConfig(): array
    {
        return $this->http->get(self::BASE . '/getDefaultJsonConfig');
    }

    public function getApiTokens(): array
    {
        return $this->http->get(self::BASE . '/apiTokens');
    }

    public function createApiToken(string $name): array
    {
        return $this->http->post(self::BASE . '/apiTokens/create', [
            'name' => $name,
        ]);
    }

    public function deleteApiToken(int $id): array
    {
        return $this->http->post(self::BASE . "/apiTokens/delete/{$id}");
    }

    public function setApiTokenEnabled(int $id, bool $enabled): array
    {
        return $this->http->post(self::BASE . "/apiTokens/setEnabled/{$id}", [
            'enabled' => $enabled,
        ]);
    }
}
