<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;
use ThreeXUI\Helpers\Validator;

class Clients implements EndpointInterface
{
    private const BASE = '/panel/api/clients';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function listPaged(array $params = []): array
    {
        return $this->http->post(self::BASE . '/list/paged', $params);
    }

    public function list(): array
    {
        return $this->http->get(self::BASE . '/list');
    }

    public function get(string $email): array
    {
        Validator::assertEmail($email);

        return $this->http->get(self::BASE . "/get/{$email}");
    }

    public function add(array $clientData, array $inboundIds): array
    {
        Validator::requiredFields($clientData, ['email']);
        Validator::assertEmail($clientData['email']);

        return $this->http->post(self::BASE . '/add', [
            'client'     => $clientData,
            'inboundIds' => $inboundIds,
        ]);
    }

    public function update(string $email, array $data): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/update/{$email}", $data);
    }

    public function delete(string $email, bool $keepTraffic = false): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/del/{$email}?keepTraffic=" . ($keepTraffic ? 'true' : 'false'));
    }

    public function attach(string $email, array $inboundIds): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/{$email}/attach", [
            'inboundIds' => $inboundIds,
        ]);
    }

    public function detach(string $email, array $inboundIds): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/{$email}/detach", [
            'inboundIds' => $inboundIds,
        ]);
    }

    public function resetAllTraffics(): array
    {
        return $this->http->post(self::BASE . '/resetAllTraffics');
    }

    public function delDepleted(): array
    {
        return $this->http->post(self::BASE . '/delDepleted');
    }

    public function bulkAdjust(array $emails, ?int $addDays = null, ?int $addBytes = null): array
    {
        $data = ['emails' => $emails];

        if ($addDays !== null) {
            $data['addDays'] = $addDays;
        }

        if ($addBytes !== null) {
            $data['addBytes'] = $addBytes;
        }

        return $this->http->post(self::BASE . '/bulkAdjust', $data);
    }

    public function bulkDelete(array $emails, bool $keepTraffic = false): array
    {
        return $this->http->post(self::BASE . '/bulkDel', [
            'emails'      => $emails,
            'keepTraffic' => $keepTraffic,
        ]);
    }

    public function bulkCreate(array $entries): array
    {
        return $this->http->post(self::BASE . '/bulkCreate', $entries);
    }

    public function bulkAttach(array $emails, array $inboundIds): array
    {
        return $this->http->post(self::BASE . '/bulkAttach', [
            'emails'      => $emails,
            'inboundIds'  => $inboundIds,
        ]);
    }

    public function bulkDetach(array $emails, array $inboundIds): array
    {
        return $this->http->post(self::BASE . '/bulkDetach', [
            'emails'      => $emails,
            'inboundIds'  => $inboundIds,
        ]);
    }

    public function bulkResetTraffic(array $emails): array
    {
        return $this->http->post(self::BASE . '/bulkResetTraffic', [
            'emails' => $emails,
        ]);
    }

    public function resetTraffic(string $email): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/resetTraffic/{$email}");
    }

    public function updateTraffic(string $email, int $upload, int $download): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/updateTraffic/{$email}", [
            'upload'   => $upload,
            'download' => $download,
        ]);
    }

    public function ips(string $email): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/ips/{$email}");
    }

    public function clearIps(string $email): array
    {
        Validator::assertEmail($email);

        return $this->http->post(self::BASE . "/clearIps/{$email}");
    }

    public function onlines(): array
    {
        return $this->http->post(self::BASE . '/onlines');
    }

    public function lastOnline(): array
    {
        return $this->http->post(self::BASE . '/lastOnline');
    }

    public function traffic(string $email): array
    {
        Validator::assertEmail($email);

        return $this->http->get(self::BASE . "/traffic/{$email}");
    }

    public function subLinks(string $subId): array
    {
        return $this->http->get(self::BASE . "/subLinks/{$subId}");
    }

    public function links(string $email): array
    {
        Validator::assertEmail($email);

        return $this->http->get(self::BASE . "/links/{$email}");
    }
}
