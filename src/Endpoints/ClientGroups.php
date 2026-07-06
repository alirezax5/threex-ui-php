<?php

declare(strict_types=1);

namespace ThreeXUI\Endpoints;

use ThreeXUI\Contracts\EndpointInterface;
use ThreeXUI\HttpClient;
use ThreeXUI\Helpers\Validator;

class ClientGroups implements EndpointInterface
{
    private const BASE = '/panel/api/clients/groups';

    public function __construct(
        private readonly HttpClient $http
    ) {}

    public function getClient(): HttpClient
    {
        return $this->http;
    }

    public function list(): array
    {
        return $this->http->get(self::BASE . '/');
    }

    public function create(string $name): array
    {
        return $this->http->post(self::BASE . '/create', [
            'name' => $name,
        ]);
    }

    public function rename(string $oldName, string $newName): array
    {
        return $this->http->post(self::BASE . '/rename', [
            'oldName' => $oldName,
            'newName' => $newName,
        ]);
    }

    public function delete(string $name): array
    {
        return $this->http->post(self::BASE . '/delete', [
            'name' => $name,
        ]);
    }

    public function bulkAdd(array $emails, string $group): array
    {
        Validator::nonEmptyArray('emails', $emails);

        return $this->http->post(self::BASE . '/bulkAdd', [
            'emails' => $emails,
            'group'  => $group,
        ]);
    }

    public function bulkRemove(array $emails): array
    {
        Validator::nonEmptyArray('emails', $emails);

        return $this->http->post(self::BASE . '/bulkRemove', [
            'emails' => $emails,
        ]);
    }

    public function emails(string $name): array
    {
        return $this->http->get(self::BASE . "/{$name}/emails");
    }
}
