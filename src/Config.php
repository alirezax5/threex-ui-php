<?php

declare(strict_types=1);

namespace ThreeXUI;

class Config
{
    private string $baseUrl;

    private ?string $apiToken = null;

    private ?string $username = null;

    private ?string $password = null;

    private int $timeout = 30;

    private bool $verifySsl = true;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function useApiToken(string $token): self
    {
        $this->apiToken = $token;
        $this->username = null;
        $this->password = null;

        return $this;
    }

    public function useCredentials(string $username, string $password): self
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiToken = null;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function disableSslVerification(): self
    {
        $this->verifySsl = false;

        return $this;
    }

    public function isSslVerified(): bool
    {
        return $this->verifySsl;
    }
}
