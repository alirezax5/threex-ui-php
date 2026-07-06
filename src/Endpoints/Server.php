<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;

class Server implements EndpointInterface
{
    private const BASE = '/panel/api/server';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function status(): array
    {
        return $this->http->get(self::BASE . '/status');
    }

    public function cpuHistory(string $bucket = '1h'): array
    {
        return $this->http->get(self::BASE . "/cpuHistory/{$bucket}");
    }

    public function history(string $metric, string $bucket = '1h'): array
    {
        return $this->http->get(self::BASE . "/history/{$metric}/{$bucket}");
    }

    public function xrayMetricsState(): array
    {
        return $this->http->get(self::BASE . '/xrayMetricsState');
    }

    public function xrayMetricsHistory(string $metric, string $bucket = '1h'): array
    {
        return $this->http->get(self::BASE . "/xrayMetricsHistory/{$metric}/{$bucket}");
    }

    public function xrayObservatory(): array
    {
        return $this->http->get(self::BASE . '/xrayObservatory');
    }

    public function xrayObservatoryHistory(string $tag, string $bucket = '1h'): array
    {
        return $this->http->get(self::BASE . "/xrayObservatoryHistory/{$tag}/{$bucket}");
    }

    public function getXrayVersion(): array
    {
        return $this->http->get(self::BASE . '/getXrayVersion');
    }

    public function getPanelUpdateInfo(): array
    {
        return $this->http->get(self::BASE . '/getPanelUpdateInfo');
    }

    public function getConfigJson(): array
    {
        return $this->http->get(self::BASE . '/getConfigJson');
    }

    public function getDb(): array
    {
        return $this->http->get(self::BASE . '/getDb');
    }

    public function getNewUUID(): array
    {
        return $this->http->get(self::BASE . '/getNewUUID');
    }

    public function getNewX25519Cert(): array
    {
        return $this->http->get(self::BASE . '/getNewX25519Cert');
    }

    public function getNewMldsa65(): array
    {
        return $this->http->get(self::BASE . '/getNewmldsa65');
    }

    public function getNewMlkem768(): array
    {
        return $this->http->get(self::BASE . '/getNewmlkem768');
    }

    public function getNewVlessEnc(): array
    {
        return $this->http->get(self::BASE . '/getNewVlessEnc');
    }

    public function stopXrayService(): array
    {
        return $this->http->post(self::BASE . '/stopXrayService');
    }

    public function restartXrayService(): array
    {
        return $this->http->post(self::BASE . '/restartXrayService');
    }

    public function installXray(string $version = 'latest'): array
    {
        return $this->http->post(self::BASE . "/installXray/{$version}");
    }

    public function updatePanel(): array
    {
        return $this->http->post(self::BASE . '/updatePanel');
    }

    public function updateGeofile(string $fileName = 'geoip.dat'): array
    {
        return $this->http->post(self::BASE . "/updateGeofile/{$fileName}");
    }

    public function updateAllGeofiles(): array
    {
        return $this->http->post(self::BASE . '/updateGeofile');
    }

    public function logs(int $count = 100, ?string $level = null, bool $syslog = false): array
    {
        $data = [];

        if ($level !== null) {
            $data['level'] = $level;
        }

        $data['syslog'] = $syslog;

        return $this->http->post(self::BASE . "/logs/{$count}", $data);
    }

    public function xrayLogs(int $count = 100): array
    {
        return $this->http->post(self::BASE . "/xraylogs/{$count}");
    }

    public function importDb(string $filePath): array
    {
        return $this->http->postMultipart(self::BASE . '/importDB', [], [
            'db' => $filePath,
        ]);
    }

    public function getNewEchCert(string $sni = ''): array
    {
        return $this->http->post(self::BASE . '/getNewEchCert', ['sni' => $sni]);
    }
}
