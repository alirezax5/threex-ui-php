<?php

declare(strict_types=1);

namespace ThreeXUI\Helpers;

use ThreeXUI\Exceptions\ConnectionException;
use ThreeXUI\Exceptions\ApiException;

class RetryHandler
{
    private int $maxAttempts;
    private int $baseDelayMs;
    private float $multiplier;
    private int $maxDelayMs;
    private bool $jitter;

    private array $retryableStatusCodes = [408, 429, 500, 502, 503, 504];

    /**
     * @param int   $maxAttempts  Maximum retry attempts (including initial try)
     * @param int   $baseDelayMs  Base delay in milliseconds before first retry
     * @param float $multiplier   Exponential backoff multiplier
     * @param int   $maxDelayMs   Maximum delay cap in milliseconds
     * @param bool  $jitter       Add random jitter to delay (prevents thundering herd)
     */
    public function __construct(
        int $maxAttempts = 3,
        int $baseDelayMs = 500,
        float $multiplier = 2.0,
        int $maxDelayMs = 10000,
        bool $jitter = true
    ) {
        $this->maxAttempts = max(1, $maxAttempts);
        $this->baseDelayMs = max(0, $baseDelayMs);
        $this->multiplier = max(1.0, $multiplier);
        $this->maxDelayMs = max(0, $maxDelayMs);
        $this->jitter = $jitter;
    }

    /**
     * Execute a callable with automatic retry on transient failures.
     *
     * @template T
     * @param callable(): T $operation
     * @return T
     *
     * @throws ConnectionException|ApiException On final failure after all retries exhausted
     */
    public function execute(callable $operation): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxAttempts) {
            $attempt++;

            try {
                return $operation();
            } catch (\Throwable $e) {
                $lastException = $e;

                if (!$this->shouldRetry($e) || $attempt >= $this->maxAttempts) {
                    throw $e;
                }
            }

            $delay = $this->calculateDelay($attempt - 1);
            usleep($delay * 1000);
        }

        throw $lastException ?? new \RuntimeException('Retry exhausted with no exception.');
    }

    /**
     * Determine if an exception is eligible for retry.
     */
    public function shouldRetry(\Throwable $e): bool
    {
        if ($e instanceof ConnectionException) {
            return true;
        }

        if ($e instanceof ApiException) {
            return in_array($e->getCode(), $this->retryableStatusCodes, true);
        }

        return false;
    }

    /**
     * Calculate delay with exponential backoff and optional jitter.
     */
    public function calculateDelay(int $retryCount): int
    {
        $delay = $this->baseDelayMs * ($this->multiplier ** $retryCount);
        $delay = min($delay, $this->maxDelayMs);

        if ($this->jitter) {
            $jitter = random_int(0, (int) ($delay * 0.3));
            $delay = $delay + $jitter;
        }

        return (int) $delay;
    }

    /**
     * Add custom retryable HTTP status codes.
     */
    public function addRetryableStatusCodes(int ...$codes): self
    {
        $this->retryableStatusCodes = array_unique([
            ...$this->retryableStatusCodes,
            ...$codes,
        ]);

        return $this;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getBaseDelayMs(): int
    {
        return $this->baseDelayMs;
    }

    public function getRetryableStatusCodes(): array
    {
        return $this->retryableStatusCodes;
    }
}
