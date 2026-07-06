<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\ClientGroups;

class ClientGroupsTest extends EndpointTestCase
{
    private ClientGroups $groups;

    protected function setUp(): void
    {
        parent::setUp();
        $this->groups = new ClientGroups($this->http);
    }

    public function test_list(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->mockGetResponse($expected);
        $result = $this->groups->list();

        $this->assertSame($expected, $result);
    }

    public function test_create(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/groups/create', ['name' => 'premium'])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->groups->create('premium');

        $this->assertTrue($result['success']);
    }

    public function test_rename(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/groups/rename', ['oldName' => 'old', 'newName' => 'new'])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->groups->rename('old', 'new');

        $this->assertTrue($result['success']);
    }

    public function test_delete(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/groups/delete', ['name' => 'temp'])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->groups->delete('temp');

        $this->assertTrue($result['success']);
    }

    public function test_bulk_add(): void
    {
        $this->http->method('post')
            ->with('/panel/api/clients/groups/bulkAdd', [
                'emails' => ['a@test.com', 'b@test.com'],
                'group'  => 'vip',
            ])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->groups->bulkAdd(['a@test.com', 'b@test.com'], 'vip');

        $this->assertTrue($result['success']);
    }

    public function test_emails(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['a@test.com']];

        $this->http->method('get')
            ->with('/panel/api/clients/groups/vip/emails')
            ->willReturn($expected);

        $result = $this->groups->emails('vip');

        $this->assertSame($expected, $result);
    }
}
