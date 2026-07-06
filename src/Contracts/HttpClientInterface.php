<?php

declare(strict_types=1);

namespace ThreeXUI\Contracts;

interface HttpClientInterface
{
    public function get(string $path, array $query = []): array;

    public function post(string $path, array $data = []): array;

    public function postMultipart(string $path, array $data, array $files = []): array;

    public function setBaseUrl(string $baseUrl): void;

    public function setBearerToken(string $token): void;

    public function setCookie(string $cookie): void;

    public function setTimeout(int $timeout): void;
}
