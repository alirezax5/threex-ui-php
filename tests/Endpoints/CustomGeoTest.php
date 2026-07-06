<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\CustomGeo;

class CustomGeoTest extends EndpointTestCase
{
    private CustomGeo $geo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geo = new CustomGeo($this->http);
    }

    public function test_list(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->mockGetResponse($expected);
        $result = $this->geo->list();

        $this->assertSame($expected, $result);
    }

    public function test_aliases(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->mockGetResponse($expected);
        $result = $this->geo->aliases();

        $this->assertSame($expected, $result);
    }

    public function test_add(): void
    {
        $this->http->method('post')
            ->with('/panel/api/custom-geo/add', [
                'type'  => 'geoip',
                'alias' => 'custom',
                'url'   => 'https://example.com/geoip.dat',
            ])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->geo->add('geoip', 'custom', 'https://example.com/geoip.dat');

        $this->assertTrue($result['success']);
    }

    public function test_delete(): void
    {
        $this->http->method('post')
            ->with('/panel/api/custom-geo/delete/3')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->geo->delete(3);

        $this->assertTrue($result['success']);
    }

    public function test_download(): void
    {
        $this->http->method('post')
            ->with('/panel/api/custom-geo/download/2')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->geo->download(2);

        $this->assertTrue($result['success']);
    }

    public function test_update_all(): void
    {
        $this->http->method('post')
            ->with('/panel/api/custom-geo/update-all')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->geo->updateAll();

        $this->assertTrue($result['success']);
    }
}
