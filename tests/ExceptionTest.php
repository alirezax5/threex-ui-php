<?php

declare(strict_types=1);

namespace ThreeXUI\Tests;

use PHPUnit\Framework\TestCase;
use ThreeXUI\Exceptions\ThreeXUIException;
use ThreeXUI\Exceptions\AuthenticationException;
use ThreeXUI\Exceptions\ApiException;
use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ValidationException;

class ExceptionTest extends TestCase
{
    public function test_threexui_exception_defaults(): void
    {
        $e = new ThreeXUIException();

        $this->assertInstanceOf(\RuntimeException::class, $e);
        $this->assertSame('', $e->getMessage());
        $this->assertSame(0, $e->getCode());
        $this->assertSame([], $e->getContext());
    }

    public function test_threexui_exception_with_context(): void
    {
        $e = new ThreeXUIException('Something went wrong', 500, null, ['key' => 'value']);

        $this->assertSame('Something went wrong', $e->getMessage());
        $this->assertSame(500, $e->getCode());
        $this->assertSame(['key' => 'value'], $e->getContext());
    }

    public function test_authentication_exception(): void
    {
        $e = new AuthenticationException('Invalid credentials');

        $this->assertInstanceOf(ThreeXUIException::class, $e);
        $this->assertSame('Invalid credentials', $e->getMessage());
    }

    public function test_api_exception_with_response_data(): void
    {
        $responseData = ['success' => false, 'msg' => 'Not found', 'obj' => null];
        $e = new ApiException('Not found', 404, $responseData);

        $this->assertInstanceOf(ThreeXUIException::class, $e);
        $this->assertSame('Not found', $e->getMessage());
        $this->assertSame(404, $e->getCode());
        $this->assertSame($responseData, $e->getResponseData());
    }

    public function test_connection_exception(): void
    {
        $e = new ConnectionException('cURL error: Connection refused', 7);

        $this->assertInstanceOf(ThreeXUIException::class, $e);
        $this->assertSame('cURL error: Connection refused', $e->getMessage());
    }

    public function test_validation_exception(): void
    {
        $e = new ValidationException('Invalid email format');

        $this->assertInstanceOf(ThreeXUIException::class, $e);
        $this->assertSame('Invalid email format', $e->getMessage());
    }
}
