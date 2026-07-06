<?php

declare(strict_types=1);

namespace ThreeXUI;

use ThreeXUI\Exceptions\AuthenticationException;

class Authentication
{
    private HttpClient $http;

    private Config $config;

    public function __construct(HttpClient $http, Config $config)
    {
        $this->http = $http;
        $this->config = $config;
    }

    public function login(): array
    {
        $username = $this->config->getUsername();
        $password = $this->config->getPassword();

        if ($username === null || $password === null) {
            throw new AuthenticationException('Username and password are required for session login.');
        }

        $response = $this->http->post('/login', [
            'username' => $username,
            'password' => $password,
        ]);

        $cookie = $this->http->getSessionCookie();

        if ($cookie) {
            $this->http->setCookie($cookie);
            $this->fetchCsrfToken();
        }

        return $response;
    }

    public function logout(): array
    {
        $response = $this->http->post('/logout');
        $this->http->setCookie(null);
        $this->http->setCsrfToken(null);

        return $response;
    }

    public function getCsrfToken(): ?string
    {
        return $this->http->getCsrfToken();
    }

    private function fetchCsrfToken(): void
    {
        try {
            $response = $this->http->get('/csrf-token');
            $this->http->setCsrfToken($response['obj'] ?? null);
        } catch (\Throwable) {
            $this->http->setCsrfToken(null);
        }
    }

    public function isTwoFactorEnabled(): bool
    {
        $response = $this->http->post('/getTwoFactorEnable');

        return (bool) ($response['obj'] ?? false);
    }
}
