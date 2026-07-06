<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\Server;

class ServerTest extends EndpointTestCase
{
    private Server $server;

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = new Server($this->http);
    }

    public function test_status(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['cpu' => 10.5, 'mem' => []]];

        $this->http->method('get')
            ->with('/panel/api/server/status')
            ->willReturn($expected);

        $result = $this->server->status();

        $this->assertSame($expected, $result);
    }

    public function test_get_new_uuid(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => '550e8400-e29b-41d4-a716-446655440000'];

        $this->http->method('get')
            ->with('/panel/api/server/getNewUUID')
            ->willReturn($expected);

        $result = $this->server->getNewUUID();

        $this->assertSame($expected, $result);
    }

    public function test_get_new_x25519_cert(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['privateKey' => 'xxx', 'publicKey' => 'yyy']];

        $this->http->method('get')
            ->with('/panel/api/server/getNewX25519Cert')
            ->willReturn($expected);

        $result = $this->server->getNewX25519Cert();

        $this->assertSame($expected, $result);
    }

    public function test_stop_xray_service(): void
    {
        $this->http->method('post')
            ->with('/panel/api/server/stopXrayService')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->server->stopXrayService();

        $this->assertTrue($result['success']);
    }

    public function test_restart_xray_service(): void
    {
        $this->http->method('post')
            ->with('/panel/api/server/restartXrayService')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->server->restartXrayService();

        $this->assertTrue($result['success']);
    }

    public function test_logs(): void
    {
        $this->http->method('post')
            ->with('/panel/api/server/logs/50', ['syslog' => false])
            ->willReturn(['success' => true, 'msg' => '', 'obj' => []]);

        $result = $this->server->logs(50);

        $this->assertTrue($result['success']);
    }

    public function test_logs_with_level(): void
    {
        $this->http->method('post')
            ->with('/panel/api/server/logs/100', ['level' => 'error', 'syslog' => false])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->server->logs(100, level: 'error');

        $this->assertTrue($result['success']);
    }

    public function test_xray_logs(): void
    {
        $this->http->method('post')
            ->with('/panel/api/server/xraylogs/200')
            ->willReturn(['success' => true, 'msg' => '', 'obj' => []]);

        $result = $this->server->xrayLogs(200);

        $this->assertTrue($result['success']);
    }

    public function test_history(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => [['t' => 1, 'v' => 0.5]]];

        $this->http->method('get')
            ->with('/panel/api/server/history/cpu/1h')
            ->willReturn($expected);

        $result = $this->server->history('cpu', '1h');

        $this->assertSame($expected, $result);
    }

    public function test_install_xray(): void
    {
        $this->http->method('post')
            ->with('/panel/api/server/installXray/latest')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->server->installXray();

        $this->assertTrue($result['success']);
    }
}
