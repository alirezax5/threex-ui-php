<?php

declare(strict_types=1);

namespace ThreeXUI;

use ThreeXUI\Contracts\HttpClientInterface;
use ThreeXUI\Exceptions\AuthenticationException;
use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ApiException;
use ThreeXUI\Helpers\RetryHandler;

class HttpClient implements HttpClientInterface
{
    private string $baseUrl;

    private ?string $bearerToken = null;

    private ?string $sessionCookie = null;

    private ?string $csrfToken = null;

    private int $timeout = 30;

    private bool $verifySsl = true;

    private ?RetryHandler $retryHandler = null;

    public function __construct(Config $config)
    {
        $this->baseUrl = $config->getBaseUrl();
        $this->timeout = $config->getTimeout();
        $this->verifySsl = $config->isSslVerified();
        $this->bearerToken = $config->getApiToken();
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function setBearerToken(string $token): void
    {
        $this->bearerToken = $token;
        $this->sessionCookie = null;
    }

    public function setCookie(string $cookie): void
    {
        $this->sessionCookie = $cookie;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function getSessionCookie(): ?string
    {
        return $this->sessionCookie;
    }

    public function setCsrfToken(?string $token): void
    {
        $this->csrfToken = $token;
    }

    public function get(string $path, array $query = []): array
    {
        $url = $this->buildUrl($path, $query);

        return $this->request('GET', $url);
    }

    public function post(string $path, array $data = []): array
    {
        $url = $this->buildUrl($path);
        $json = $data !== [] ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;

        return $this->request('POST', $url, $json);
    }

    public function postMultipart(string $path, array $data, array $files = []): array
    {
        $url = $this->buildUrl($path);
        $body = [];

        foreach ($data as $key => $value) {
            $body[$key] = $value;
        }

        foreach ($files as $key => $filePath) {
            $body[$key] = new \CURLFile($filePath);
        }

        return $this->request('POST', $url, $body, true);
    }

    public function put(string $path, array $data = []): array
    {
        $url = $this->buildUrl($path);
        $json = $data !== [] ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;

        return $this->request('PUT', $url, $json);
    }

    public function delete(string $path): array
    {
        return $this->request('DELETE', $this->buildUrl($path));
    }

    private function buildUrl(string $path, array $query = []): string
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    private function request(string $method, string $url, mixed $body = null, bool $multipart = false): array
    {
        $doRequest = function () use ($method, $url, $body, $multipart): array {
            return $this->executeCurl($method, $url, $body, $multipart);
        };

        if ($this->retryHandler !== null) {
            return $this->retryHandler->execute($doRequest);
        }

        return $doRequest();
    }

    /**
     * Raw cURL execution — retries wrap this.
     */
    private function executeCurl(string $method, string $url, mixed $body, bool $multipart): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);

        $headers = ['Accept: application/json'];

        if ($this->bearerToken) {
            $headers[] = 'Authorization: Bearer ' . $this->bearerToken;
        }

        if ($this->sessionCookie) {
            $headers[] = 'Cookie: ' . $this->sessionCookie;
        }

        if ($method === 'POST' || $method === 'PUT') {
            if ($multipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                $headers[] = 'Content-Type: multipart/form-data';
            } elseif ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                $headers[] = 'Content-Type: application/json; charset=utf-8';
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new ConnectionException("cURL error: {$error}", curl_errno($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $responseHeaders = substr($response, 0, $headerSize);
        $responseBody = substr($response, $headerSize);

        $this->extractCookie($responseHeaders);

        if ($httpCode === 401) {
            throw new AuthenticationException('Authentication failed. Check your credentials or API token.');
        }

        $data = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConnectionException("Invalid JSON response: {$responseBody}");
        }

        if ($httpCode >= 400) {
            $msg = $data['msg'] ?? "HTTP {$httpCode}";
            throw new ApiException($msg, $httpCode, $data);
        }

        return $data;
    }

    private function extractCookie(string $headers): void
    {
        if (preg_match('/Set-Cookie:\s*([^;]+)/i', $headers, $matches)) {
            $this->sessionCookie = $matches[1];
        }
    }

    public function setRetryHandler(?RetryHandler $retryHandler): self
    {
        $this->retryHandler = $retryHandler;

        return $this;
    }

    public function getRetryHandler(): ?RetryHandler
    {
        return $this->retryHandler;
    }

    public function getCsrfToken(): ?string
    {
        return $this->csrfToken;
    }
}
