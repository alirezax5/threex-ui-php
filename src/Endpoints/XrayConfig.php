<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;

class XrayConfig implements EndpointInterface
{
    private const BASE = '/panel/xray';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function getConfig(): array
    {
        return $this->http->post(self::BASE . '/');
    }

    public function getDefaultJsonConfig(): array
    {
        return $this->http->get(self::BASE . '/getDefaultJsonConfig');
    }

    public function getOutboundsTraffic(): array
    {
        return $this->http->get(self::BASE . '/getOutboundsTraffic');
    }

    public function getXrayResult(): array
    {
        return $this->http->get(self::BASE . '/getXrayResult');
    }

    public function update(array $fields): array
    {
        return $this->http->post(self::BASE . '/update', $fields);
    }

    public function warp(string $action): array
    {
        return $this->http->post(self::BASE . "/warp/{$action}");
    }

    public function nord(string $action): array
    {
        return $this->http->post(self::BASE . "/nord/{$action}");
    }

    public function resetOutboundsTraffic(?string $tag = null): array
    {
        $data = [];

        if ($tag !== null) {
            $data['tag'] = $tag;
        }

        return $this->http->post(self::BASE . '/resetOutboundsTraffic', $data);
    }

    public function testOutbound(array $config): array
    {
        return $this->http->post(self::BASE . '/testOutbound', $config);
    }
}
