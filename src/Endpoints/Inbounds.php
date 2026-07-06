<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;
use ThreeXUI\Helpers\Validator;

class Inbounds implements EndpointInterface
{
    private const BASE = '/panel/api/inbounds';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function listSlim(): array
    {
        return $this->http->get(self::BASE . '/list/slim');
    }

    public function list(): array
    {
        return $this->http->get(self::BASE . '/list');
    }

    public function options(): array
    {
        return $this->http->get(self::BASE . '/options');
    }

    public function get(int $id): array
    {
        return $this->http->get(self::BASE . "/get/{$id}");
    }

    public function add(array $data): array
    {
        Validator::requiredFields($data, ['remark', 'port', 'protocol', 'settings']);
        Validator::assertPort($data['port']);
        Validator::assertProtocol($data['protocol']);

        return $this->http->post(self::BASE . '/add', $data);
    }

    public function delete(int $id): array
    {
        return $this->http->post(self::BASE . "/del/{$id}");
    }

    public function update(int $id, array $data): array
    {
        return $this->http->post(self::BASE . "/update/{$id}", $data);
    }

    public function setEnable(int $id, bool $enable): array
    {
        return $this->http->post(self::BASE . "/setEnable/{$id}", [
            'enable' => $enable,
        ]);
    }

    public function resetTraffic(int $id): array
    {
        return $this->http->post(self::BASE . "/{$id}/resetTraffic");
    }

    public function delAllClients(int $id): array
    {
        return $this->http->post(self::BASE . "/{$id}/delAllClients");
    }

    public function getFallbacks(int $id): array
    {
        return $this->http->get(self::BASE . "/{$id}/fallbacks");
    }

    public function setFallbacks(int $id, array $fallbacks): array
    {
        return $this->http->post(self::BASE . "/{$id}/fallbacks", [
            'fallbacks' => $fallbacks,
        ]);
    }

    public function resetAllTraffics(): array
    {
        return $this->http->post(self::BASE . '/resetAllTraffics');
    }

    public function import(string $jsonData): array
    {
        return $this->http->post(self::BASE . '/import', [
            'data' => $jsonData,
        ]);
    }
}
