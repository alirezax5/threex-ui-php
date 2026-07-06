<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;
use ThreeXUI\Helpers\Validator;

class CustomGeo implements EndpointInterface
{
    private const BASE = '/panel/api/custom-geo';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function list(): array
    {
        return $this->http->get(self::BASE . '/list');
    }

    public function aliases(): array
    {
        return $this->http->get(self::BASE . '/aliases');
    }

    public function add(string $type, string $alias, string $url): array
    {
        return $this->http->post(self::BASE . '/add', [
            'type'  => $type,
            'alias' => $alias,
            'url'   => $url,
        ]);
    }

    public function update(int $id, array $data): array
    {
        return $this->http->post(self::BASE . "/update/{$id}", $data);
    }

    public function delete(int $id): array
    {
        return $this->http->post(self::BASE . "/delete/{$id}");
    }

    public function download(int $id): array
    {
        return $this->http->post(self::BASE . "/download/{$id}");
    }

    public function updateAll(): array
    {
        return $this->http->post(self::BASE . '/update-all');
    }
}
