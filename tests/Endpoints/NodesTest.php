<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\Nodes;
use ThreeXUI\Exceptions\ValidationException;

class NodesTest extends EndpointTestCase
{
    private Nodes $nodes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nodes = new Nodes($this->http);
    }

    public function test_list(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->mockGetResponse($expected);
        $result = $this->nodes->list();

        $this->assertSame($expected, $result);
    }

    public function test_get(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['id' => 1, 'name' => 'Node 1']];

        $this->http->method('get')
            ->with('/panel/api/nodes/get/1')
            ->willReturn($expected);

        $result = $this->nodes->get(1);

        $this->assertSame($expected, $result);
    }

    public function test_add_validates_required_fields(): void
    {
        $this->expectException(ValidationException::class);

        $this->nodes->add(['name' => 'Test']);
    }

    public function test_add_node(): void
    {
        $nodeData = [
            'name'     => 'Node 1',
            'scheme'   => 'https',
            'address'  => '1.2.3.4',
            'port'     => 54321,
            'basePath' => '/',
            'apiToken' => 'token123',
        ];

        $this->http->method('post')
            ->with('/panel/api/nodes/add', $nodeData)
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->nodes->add($nodeData);

        $this->assertTrue($result['success']);
    }

    public function test_delete(): void
    {
        $this->http->method('post')
            ->with('/panel/api/nodes/del/5')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->nodes->delete(5);

        $this->assertTrue($result['success']);
    }

    public function test_set_enable(): void
    {
        $this->http->method('post')
            ->with('/panel/api/nodes/setEnable/3', ['enable' => false])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->nodes->setEnable(3, false);

        $this->assertTrue($result['success']);
    }

    public function test_test_connection_validates(): void
    {
        $this->expectException(ValidationException::class);

        $this->nodes->test([]);
    }

    public function test_test_connection(): void
    {
        $connData = [
            'scheme'   => 'https',
            'address'  => '1.2.3.4',
            'port'     => 54321,
            'basePath' => '/',
            'apiToken' => 'token',
        ];

        $this->http->method('post')
            ->with('/panel/api/nodes/test', $connData)
            ->willReturn(['success' => true, 'msg' => 'Connection successful']);

        $result = $this->nodes->test($connData);

        $this->assertTrue($result['success']);
    }
}
