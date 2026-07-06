<?php

declare(strict_types=1);

namespace ThreeXUI\Exceptions;

class ApiException extends ThreeXUIException
{
    private array $responseData;

    public function __construct(string $message = '', int $code = 0, array $responseData = [])
    {
        parent::__construct($message, $code);
        $this->responseData = $responseData;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }
}
