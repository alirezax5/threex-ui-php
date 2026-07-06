<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\XrayConfig;

class XrayConfigTest extends EndpointTestCase
{
    private XrayConfig $xray;

    protected function setUp(): void
    {
        parent::setUp();
        $this->xray = new XrayConfig($this->http);
    }

    public function test_get_config(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->http->method('post')
            ->with('/panel/xray/')
            ->willReturn($expected);

        $result = $this->xray->getConfig();

        $this->assertSame($expected, $result);
    }

    public function test_get_outbounds_traffic(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->http->method('get')
            ->with('/panel/xray/getOutboundsTraffic')
            ->willReturn($expected);

        $result = $this->xray->getOutboundsTraffic();

        $this->assertSame($expected, $result);
    }

    public function test_get_xray_result(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => 'Xray 24.11.21 started'];

        $this->http->method('get')
            ->with('/panel/xray/getXrayResult')
            ->willReturn($expected);

        $result = $this->xray->getXrayResult();

        $this->assertSame($expected, $result);
    }

    public function test_warp(): void
    {
        $this->http->method('post')
            ->with('/panel/xray/warp/start')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->xray->warp('start');

        $this->assertTrue($result['success']);
    }

    public function test_nord(): void
    {
        $this->http->method('post')
            ->with('/panel/xray/nord/connect')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->xray->nord('connect');

        $this->assertTrue($result['success']);
    }

    public function test_reset_outbounds_traffic(): void
    {
        $this->http->method('post')
            ->with('/panel/xray/resetOutboundsTraffic', ['tag' => 'proxy'])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->xray->resetOutboundsTraffic('proxy');

        $this->assertTrue($result['success']);
    }

    public function test_reset_outbounds_traffic_all(): void
    {
        $this->http->method('post')
            ->with('/panel/xray/resetOutboundsTraffic', [])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->xray->resetOutboundsTraffic();

        $this->assertTrue($result['success']);
    }

    public function test_test_outbound(): void
    {
        $config = ['protocol' => 'freedom', 'settings' => []];

        $this->http->method('post')
            ->with('/panel/xray/testOutbound', $config)
            ->willReturn(['success' => true, 'msg' => '', 'obj' => ['latency' => 45]]);

        $result = $this->xray->testOutbound($config);

        $this->assertTrue($result['success']);
        $this->assertSame(45, $result['obj']['latency']);
    }
}
