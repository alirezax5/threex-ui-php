<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Helpers;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function test_threexui_client(): void
    {
        $client = \threexui_client('https://panel.example.com:54321');

        $this->assertInstanceOf(\ThreeXUI\ThreeXUI::class, $client);
    }

    public function test_threexui_client_with_token(): void
    {
        $client = \threexui_client('https://panel.example.com:54321', 'token123');

        $this->assertInstanceOf(\ThreeXUI\ThreeXUI::class, $client);
        $this->assertSame('token123', $client->getConfig()->getApiToken());
    }

    public function test_bytes_to_human_helper(): void
    {
        $this->assertSame('1 GB', \bytes_to_human(1073741824));
    }

    public function test_human_to_bytes_helper(): void
    {
        $this->assertSame(1073741824, \human_to_bytes('1 GB'));
    }

    public function test_gb_to_bytes_helper(): void
    {
        $this->assertSame(1073741824, \gb_to_bytes(1.0));
    }

    public function test_bytes_to_gb_helper(): void
    {
        $this->assertSame(1.0, \bytes_to_gb(1073741824));
    }

    public function test_array_dot_get_helper(): void
    {
        $data = ['a' => ['b' => 'value']];

        $this->assertSame('value', \array_dot_get($data, 'a.b'));
        $this->assertNull(\array_dot_get($data, 'x.y'));
        $this->assertSame('fallback', \array_dot_get($data, 'x', 'fallback'));
    }

    public function test_validate_uuid_helper(): void
    {
        $this->assertTrue(\validate_uuid('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertFalse(\validate_uuid('invalid'));
    }

    public function test_validate_protocol_helper(): void
    {
        $this->assertTrue(\validate_protocol('vless'));
        $this->assertFalse(\validate_protocol('ftp'));
    }

    public function test_validate_port_helper(): void
    {
        $this->assertTrue(\validate_port(443));
        $this->assertFalse(\validate_port(0));
    }
}
