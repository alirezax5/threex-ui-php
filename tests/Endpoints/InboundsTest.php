<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\Inbounds;
use ThreeXUI\Exceptions\ValidationException;

class InboundsTest extends EndpointTestCase
{
    private Inbounds $inbounds;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inbounds = new Inbounds($this->http);
    }

    public function test_list_returns_data(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => [['id' => 1, 'remark' => 'Test']]];

        $this->mockGetResponse($expected);

        $result = $this->inbounds->list();

        $this->assertSame($expected, $result);
    }

    public function test_list_slim(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->mockGetResponse($expected);

        $result = $this->inbounds->listSlim();

        $this->assertSame($expected, $result);
    }

    public function test_options(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->mockGetResponse($expected);

        $result = $this->inbounds->options();

        $this->assertSame($expected, $result);
    }

    public function test_get_by_id(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['id' => 5, 'remark' => 'My Inbound']];

        $this->http->method('get')
            ->with('/panel/api/inbounds/get/5')
            ->willReturn($expected);

        $result = $this->inbounds->get(5);

        $this->assertSame($expected, $result);
    }

    public function test_add_validates_required_fields(): void
    {
        $this->expectException(ValidationException::class);

        $this->inbounds->add([]);
    }

    public function test_add_validates_port(): void
    {
        $this->expectException(ValidationException::class);

        $this->inbounds->add([
            'remark'   => 'Test',
            'port'     => 999999,
            'protocol' => 'vless',
            'settings' => '{}',
        ]);
    }

    public function test_add_validates_protocol(): void
    {
        $this->expectException(ValidationException::class);

        $this->inbounds->add([
            'remark'   => 'Test',
            'port'     => 443,
            'protocol' => 'invalid',
            'settings' => '{}',
        ]);
    }

    public function test_delete(): void
    {
        $this->http->method('post')
            ->with('/panel/api/inbounds/del/10')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->inbounds->delete(10);

        $this->assertTrue($result['success']);
    }

    public function test_set_enable(): void
    {
        $this->http->method('post')
            ->with('/panel/api/inbounds/setEnable/1', ['enable' => true])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->inbounds->setEnable(1, true);

        $this->assertTrue($result['success']);
    }

    public function test_reset_traffic(): void
    {
        $this->http->method('post')
            ->with('/panel/api/inbounds/1/resetTraffic')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->inbounds->resetTraffic(1);

        $this->assertTrue($result['success']);
    }

    public function test_del_all_clients(): void
    {
        $this->http->method('post')
            ->with('/panel/api/inbounds/3/delAllClients')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->inbounds->delAllClients(3);

        $this->assertTrue($result['success']);
    }

    public function test_get_fallbacks(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->http->method('get')
            ->with('/panel/api/inbounds/1/fallbacks')
            ->willReturn($expected);

        $result = $this->inbounds->getFallbacks(1);

        $this->assertSame($expected, $result);
    }

    public function test_reset_all_traffics(): void
    {
        $this->http->method('post')
            ->with('/panel/api/inbounds/resetAllTraffics')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->inbounds->resetAllTraffics();

        $this->assertTrue($result['success']);
    }

    public function test_get_client_returns_http(): void
    {
        $this->assertSame($this->http, $this->inbounds->getClient());
    }
}
