<?php

declare(strict_types=1);

namespace ThreeXUI\Contracts;

interface EndpointInterface
{
    public function getClient(): \ThreeXUI\HttpClient;
}
