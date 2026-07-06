<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Endpoints;

use ThreeXUI\Endpoints\Settings;

class SettingsTest extends EndpointTestCase
{
    private Settings $settings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settings = new Settings($this->http);
    }

    public function test_all(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => ['webPort' => 54321]];

        $this->http->method('post')
            ->with('/panel/setting/all')
            ->willReturn($expected);

        $result = $this->settings->all();

        $this->assertSame($expected, $result);
    }

    public function test_update_user(): void
    {
        $this->http->method('post')
            ->with('/panel/setting/updateUser', [
                'oldUsername' => 'admin',
                'oldPassword' => 'old',
                'newUsername' => 'newadmin',
                'newPassword' => 'new',
            ])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->settings->updateUser('admin', 'old', 'newadmin', 'new');

        $this->assertTrue($result['success']);
    }

    public function test_restart_panel(): void
    {
        $this->http->method('post')
            ->with('/panel/setting/restartPanel')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->settings->restartPanel();

        $this->assertTrue($result['success']);
    }

    public function test_get_api_tokens(): void
    {
        $expected = ['success' => true, 'msg' => '', 'obj' => []];

        $this->http->method('get')
            ->with('/panel/setting/apiTokens')
            ->willReturn($expected);

        $result = $this->settings->getApiTokens();

        $this->assertSame($expected, $result);
    }

    public function test_create_api_token(): void
    {
        $this->http->method('post')
            ->with('/panel/setting/apiTokens/create', ['name' => 'my-token'])
            ->willReturn(['success' => true, 'msg' => '', 'obj' => ['token' => 'abc123']]);

        $result = $this->settings->createApiToken('my-token');

        $this->assertTrue($result['success']);
    }

    public function test_delete_api_token(): void
    {
        $this->http->method('post')
            ->with('/panel/setting/apiTokens/delete/1')
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->settings->deleteApiToken(1);

        $this->assertTrue($result['success']);
    }

    public function test_set_api_token_enabled(): void
    {
        $this->http->method('post')
            ->with('/panel/setting/apiTokens/setEnabled/1', ['enabled' => false])
            ->willReturn(['success' => true, 'msg' => '']);

        $result = $this->settings->setApiTokenEnabled(1, false);

        $this->assertTrue($result['success']);
    }
}
