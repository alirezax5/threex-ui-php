<?php

declare(strict_types=1);

namespace ThreeXUI\Tests;

use PHPUnit\Framework\TestCase;
use ThreeXUI\Config;

class ConfigTest extends TestCase
{
    public function test_constructor_sets_base_url(): void
    {
        $config = new Config('https://panel.example.com:54321');

        $this->assertSame('https://panel.example.com:54321', $config->getBaseUrl());
    }

    public function test_constructor_trims_trailing_slash(): void
    {
        $config = new Config('https://panel.example.com:54321/');

        $this->assertSame('https://panel.example.com:54321', $config->getBaseUrl());
    }

    public function test_use_api_token_sets_token_and_clears_credentials(): void
    {
        $config = new Config('https://panel.example.com');
        $config->useApiToken('my-token');

        $this->assertSame('my-token', $config->getApiToken());
        $this->assertNull($config->getUsername());
        $this->assertNull($config->getPassword());
    }

    public function test_use_credentials_sets_creds_and_clears_token(): void
    {
        $config = new Config('https://panel.example.com');
        $config->useApiToken('old-token');
        $config->useCredentials('admin', 'pass123');

        $this->assertSame('admin', $config->getUsername());
        $this->assertSame('pass123', $config->getPassword());
        $this->assertNull($config->getApiToken());
    }

    public function test_default_timeout_is_30(): void
    {
        $config = new Config('https://panel.example.com');

        $this->assertSame(30, $config->getTimeout());
    }

    public function test_set_timeout(): void
    {
        $config = new Config('https://panel.example.com');
        $config->setTimeout(60);

        $this->assertSame(60, $config->getTimeout());
    }

    public function test_default_ssl_verification_enabled(): void
    {
        $config = new Config('https://panel.example.com');

        $this->assertTrue($config->isSslVerified());
    }

    public function test_disable_ssl_verification(): void
    {
        $config = new Config('https://panel.example.com');
        $config->disableSslVerification();

        $this->assertFalse($config->isSslVerified());
    }

    public function test_fluent_interface(): void
    {
        $config = new Config('https://panel.example.com');
        $result = $config->setTimeout(90)->disableSslVerification();

        $this->assertSame($config, $result);
        $this->assertSame(90, $config->getTimeout());
        $this->assertFalse($config->isSslVerified());
    }
}
