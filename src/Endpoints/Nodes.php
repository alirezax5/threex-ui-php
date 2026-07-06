<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;
use ThreeXUI\Helpers\Validator;

class Nodes implements EndpointInterface
{
    private const BASE = '/panel/api/nodes';

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

    public function get(int $id): array
    {
        return $this->http->get(self::BASE . "/get/{$id}");
    }

    public function add(array $data): array
    {
        Validator::requiredFields($data, ['name', 'scheme', 'address', 'port', 'basePath', 'apiToken']);

        return $this->http->post(self::BASE . '/add', $data);
    }

    public function update(int $id, array $data): array
    {
        return $this->http->post(self::BASE . "/update/{$id}", $data);
    }

    public function delete(int $id): array
    {
        return $this->http->post(self::BASE . "/del/{$id}");
    }

    public function setEnable(int $id, bool $enable): array
    {
        return $this->http->post(self::BASE . "/setEnable/{$id}", [
            'enable' => $enable,
        ]);
    }

    public function test(array $connectionData): array
    {
        Validator::requiredFields($connectionData, ['scheme', 'address', 'port', 'basePath', 'apiToken']);

        return $this->http->post(self::BASE . '/test', $connectionData);
    }

    public function probe(int $id): array
    {
        return $this->http->post(self::BASE . "/probe/{$id}");
    }

    public function history(int $id, string $metric, string $bucket = '1h'): array
    {
        return $this->http->get(self::BASE . "/history/{$id}/{$metric}/{$bucket}");
    }
}
