<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use PHPUnit\Framework\TestCase;
use ThreeXUI\HttpClient;
use ThreeXUI\Config;

abstract class EndpointTestCase extends TestCase
{
    protected HttpClient $http;

    protected function setUp(): void
    {
        $config = new Config('https://panel.example.com:54321');

        $this->http = $this->getMockBuilder(HttpClient::class)
            ->setConstructorArgs([$config])
            ->onlyMethods(['get', 'post', 'postMultipart', 'put', 'delete'])
            ->getMock();
    }

    protected function mockGetResponse(array $response): void
    {
        $this->http->method('get')->willReturn($response);
    }

    protected function mockPostResponse(array $response): void
    {
        $this->http->method('post')->willReturn($response);
    }
}
