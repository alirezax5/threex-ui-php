<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use ThreeXUI\Helpers\RetryHandler;
use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ApiException;
use ThreeXUI\Exceptions\AuthenticationException;

class RetryHandlerTest extends TestCase
{
    public function test_success_on_first_attempt(): void
    {
        $retry = new RetryHandler();
        $callCount = 0;

        $result = $retry->execute(function () use (&$callCount) {
            $callCount++;

            return 'success';
        });

        $this->assertSame('success', $result);
        $this->assertSame(1, $callCount);
    }

    public function test_retries_on_connection_exception(): void
    {
        $retry = new RetryHandler(maxAttempts: 3, baseDelayMs: 10);
        $callCount = 0;

        $result = $retry->execute(function () use (&$callCount) {
            $callCount++;

            if ($callCount < 3) {
                throw new ConnectionException('Network timeout', CURLE_OPERATION_TIMEDOUT);
            }

            return 'recovered';
        });

        $this->assertSame('recovered', $result);
        $this->assertSame(3, $callCount);
    }

    public function test_retries_on_5xx_api_exception(): void
    {
        $retry = new RetryHandler(maxAttempts: 3, baseDelayMs: 10);
        $callCount = 0;

        $result = $retry->execute(function () use (&$callCount) {
            $callCount++;

            if ($callCount < 2) {
                throw new ApiException('Server error', 503, []);
            }

            return 'ok';
        });

        $this->assertSame('ok', $result);
        $this->assertSame(2, $callCount);
    }

    public function test_retries_on_429_too_many_requests(): void
    {
        $retry = new RetryHandler(maxAttempts: 3, baseDelayMs: 10);
        $callCount = 0;

        $result = $retry->execute(function () use (&$callCount) {
            $callCount++;

            if ($callCount < 3) {
                throw new ApiException('Rate limited', 429, []);
            }

            return 'finally';
        });

        $this->assertSame('finally', $result);
        $this->assertSame(3, $callCount);
    }

    public function test_does_not_retry_on_authentication_exception(): void
    {
        $retry = new RetryHandler(maxAttempts: 3, baseDelayMs: 10);
        $callCount = 0;

        $this->expectException(AuthenticationException::class);

        $retry->execute(function () use (&$callCount) {
            $callCount++;
            throw new AuthenticationException('Invalid token');
        });
    }

    public function test_does_not_retry_on_4xx_api_exception(): void
    {
        $retry = new RetryHandler(maxAttempts: 3, baseDelayMs: 10);
        $callCount = 0;

        $this->expectException(ApiException::class);

        $retry->execute(function () use (&$callCount) {
            $callCount++;
            throw new ApiException('Bad request', 400, []);
        });
    }

    public function test_does_not_retry_on_404(): void
    {
        $retry = new RetryHandler(maxAttempts: 3, baseDelayMs: 10);
        $callCount = 0;

        $this->expectException(ApiException::class);

        $retry->execute(function () use (&$callCount) {
            $callCount++;
            throw new ApiException('Not found', 404, []);
        });
    }

    public function test_exhausts_retries_and_throws_last_exception(): void
    {
        $retry = new RetryHandler(maxAttempts: 2, baseDelayMs: 10);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Always fails');

        $retry->execute(function () {
            throw new ConnectionException('Always fails');
        });
    }

    public function test_should_retry_connection_exception(): void
    {
        $retry = new RetryHandler();

        $this->assertTrue($retry->shouldRetry(new ConnectionException('timeout')));
    }

    public function test_should_not_retry_authentication_exception(): void
    {
        $retry = new RetryHandler();

        $this->assertFalse($retry->shouldRetry(new AuthenticationException('bad creds')));
    }

    public function test_add_retryable_status_codes(): void
    {
        $retry = new RetryHandler();
        $retry->addRetryableStatusCodes(418, 422);

        $codes = $retry->getRetryableStatusCodes();

        $this->assertContains(418, $codes);
        $this->assertContains(422, $codes);
        $this->assertContains(503, $codes);
    }

    public function test_calculate_delay_increases_exponentially(): void
    {
        $retry = new RetryHandler(baseDelayMs: 100, multiplier: 2.0, jitter: false);

        $delay0 = $retry->calculateDelay(0);
        $delay1 = $retry->calculateDelay(1);
        $delay2 = $retry->calculateDelay(2);

        $this->assertSame(100, $delay0);
        $this->assertSame(200, $delay1);
        $this->assertSame(400, $delay2);
    }

    public function test_calculate_delay_capped_at_max(): void
    {
        $retry = new RetryHandler(baseDelayMs: 1000, multiplier: 10.0, maxDelayMs: 5000, jitter: false);

        $delay = $retry->calculateDelay(5);

        $this->assertSame(5000, $delay);
    }

    public function test_calculate_delay_with_jitter(): void
    {
        $retry = new RetryHandler(baseDelayMs: 100, jitter: true);

        $delay = $retry->calculateDelay(0);

        $this->assertGreaterThanOrEqual(100, $delay);
        $this->assertLessThanOrEqual(130, $delay);
    }

    public function test_max_attempts_at_least_one(): void
    {
        $retry = new RetryHandler(maxAttempts: -5);

        $this->assertSame(1, $retry->getMaxAttempts());
    }

    public function test_base_delay_ms_at_least_zero(): void
    {
        $retry = new RetryHandler(baseDelayMs: -100);

        $this->assertSame(0, $retry->getBaseDelayMs());
    }
}
