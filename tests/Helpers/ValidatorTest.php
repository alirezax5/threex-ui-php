<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use ThreeXUI\Helpers\Validator;
use ThreeXUI\Exceptions\ValidationException;

class ValidatorTest extends TestCase
{
    public function test_required_fields_pass(): void
    {
        Validator::requiredFields(['name' => 'John', 'email' => 'john@test.com'], ['name', 'email']);

        $this->assertTrue(true);
    }

    public function test_required_fields_missing_key(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing required field: email');

        Validator::requiredFields(['name' => 'John'], ['name', 'email']);
    }

    public function test_required_fields_empty_value(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Field 'name' cannot be empty.");

        Validator::requiredFields(['name' => '', 'email' => 'a@b.com'], ['name', 'email']);
    }

    public function test_required_fields_null_value(): void
    {
        $this->expectException(ValidationException::class);

        Validator::requiredFields(['name' => null], ['name']);
    }

    public function test_is_valid_email(): void
    {
        $this->assertTrue(Validator::isValidEmail('user@example.com'));
        $this->assertTrue(Validator::isValidEmail('a+b@sub.domain.co'));
        $this->assertFalse(Validator::isValidEmail('invalid'));
        $this->assertFalse(Validator::isValidEmail(''));
    }

    public function test_assert_email_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid email address: bad');

        Validator::assertEmail('bad');
    }

    public function test_is_valid_port(): void
    {
        $this->assertTrue(Validator::isValidPort(1));
        $this->assertTrue(Validator::isValidPort(443));
        $this->assertTrue(Validator::isValidPort(65535));
        $this->assertFalse(Validator::isValidPort(0));
        $this->assertFalse(Validator::isValidPort(65536));
        $this->assertFalse(Validator::isValidPort(-1));
        $this->assertFalse(Validator::isValidPort('80'));
    }

    public function test_assert_port_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid port number: 0');

        Validator::assertPort(0);
    }

    public function test_is_valid_protocol(): void
    {
        $this->assertTrue(Validator::isValidProtocol('vless'));
        $this->assertTrue(Validator::isValidProtocol('vmess'));
        $this->assertTrue(Validator::isValidProtocol('trojan'));
        $this->assertTrue(Validator::isValidProtocol('shadowsocks'));
        $this->assertTrue(Validator::isValidProtocol('VLESS'));
        $this->assertFalse(Validator::isValidProtocol('invalid'));
    }

    public function test_assert_protocol_throws(): void
    {
        $this->expectException(ValidationException::class);

        Validator::assertProtocol('ssh');
    }

    public function test_is_positive_int(): void
    {
        $this->assertTrue(Validator::isPositiveInt(0));
        $this->assertTrue(Validator::isPositiveInt(100));
        $this->assertFalse(Validator::isPositiveInt(-1));
        $this->assertFalse(Validator::isPositiveInt('100'));
    }

    public function test_is_uuid(): void
    {
        $this->assertTrue(Validator::isUuid('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertFalse(Validator::isUuid('not-a-uuid'));
        $this->assertFalse(Validator::isUuid(''));
    }

    public function test_non_empty_array(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('items must be a non-empty array.');

        Validator::nonEmptyArray('items', []);
    }
}
