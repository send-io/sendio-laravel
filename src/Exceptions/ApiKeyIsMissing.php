<?php

namespace Sendio\Laravel\Exceptions;

use InvalidArgumentException;

final class ApiKeyIsMissing extends InvalidArgumentException
{
    /**
     * Create a new APIKeyIsMissing expection instance.
     */
    public static function create(): self
    {
        return new self(
            "The send.io API key is missing. Please set the SENDIO_API_KEY variable in your application's .env file"
        );
    }
}
