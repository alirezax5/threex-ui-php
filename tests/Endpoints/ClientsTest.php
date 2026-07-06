<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\Clients;
use ThreeXUI\Exceptions\ValidationException;

class ClientsTest extends EndpointTestCase
{
    private Clients $clients;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clients = new Clients($this->http);
    }

    public function test_list_paged(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['total' => 50]];

        $this->http->method('post')
            ->with('/panel/api/clients/list/paged', ['page' => 1, 'pageSize' => 20])
            ->willReturn($expected);

        $result = $this->clients->listPaged(['page' => 1, 'pageSize' => 20]);

        $this->assertSame($expected, $result);
    }

    public function test_get_by_email(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['email' => 'user@test.com']];

        $this->http->method('get')
            ->with('/panel/api/clients/get/user@test.com')
            ->willReturn($expected);

        $result = $this->clients->get('user@test.com');

        $this->assertSame($expected, $result);
    }

    public function test_add_client(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => null];

        $this->http->method('post')
            ->with('/panel/api/clients/add', [
                'client'     => ['email' => 'new@test.com', 'totalGB' => 50],
                'inboundIds' => [1],
            ])
            ->willReturn($expected);

        $result = $this->clients->add(['email' => 'new@test.com', 'totalGB' => 50], [1]);

        $this->assertTrue($result['success']);
    }

    public function test_update_client(): void
    {
        $expected = ['success' => true, 'msg' => ''];

        $this->http->method('post')
            ->with('/panel/api/clients/update/user@test.com', ['email' => 'user@test.com', 'enable' => false])
            ->willReturn($expected);

        $result = $this->clients->update('user@test.com', ['email' => 'user@test.com', 'enable' => false]);

        $this->assertTrue($result['success']);
    }

    public function test_delete_client(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/del/user@test.com?keepTraffic=false')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->clients->delete('user@test.com');

        $this->assertTrue($result['success']);
    }

    public function test_delete_client_keep_traffic(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/del/user@test.com?keepTraffic=true')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->clients->delete('user@test.com', true);

        $this->assertTrue($result['success']);
    }

    public function test_bulk_adjust(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/bulkAdjust', [
                'emails'  => ['a@test.com', 'b@test.com'],
                'addDays' => 30,
            ])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->clients->bulkAdjust(['a@test.com', 'b@test.com'], addDays: 30);

        $this->assertTrue($result['success']);
    }

    public function test_onlines(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['online@test.com']];

        $this->http->method('post')
            ->with('/panel/api/clients/onlines')
            ->willReturn($expected);

        $result = $this->clients->onlines();

        $this->assertSame($expected, $result);
    }

    public function test_traffic(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['up' => 1024, 'down' => 2048]];

        $this->http->method('get')
            ->with('/panel/api/clients/traffic/user@test.com')
            ->willReturn($expected);

        $result = $this->clients->traffic('user@test.com');

        $this->assertSame($expected, $result);
    }

    public function test_links(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->http->method('get')
            ->with('/panel/api/clients/links/user@test.com')
            ->willReturn($expected);

        $result = $this->clients->links('user@test.com');

        $this->assertSame($expected, $result);
    }

    public function test_bulk_delete(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/bulkDel', [
                'emails'      => ['a@test.com'],
                'keepTraffic' => false,
            ])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->clients->bulkDelete(['a@test.com']);

        $this->assertTrue($result['success']);
    }

    public function test_bulk_reset_traffic(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/bulkResetTraffic', ['emails' => ['a@test.com']])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->clients->bulkResetTraffic(['a@test.com']);

        $this->assertTrue($result['success']);
    }
}
