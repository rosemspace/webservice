<?php

namespace Rosem\Component\Authentication\Exception;

use Rosem\Contract\Authentication\AuthenticationExceptionInterface;
use RuntimeException;

class AuthenticationException extends RuntimeException implements AuthenticationExceptionInterface
{
    /**
     * Throws when authentication is used over HTTP instead of HTTPS.
     *
     * @return self
     */
    public static function dueToWebServerInsecureHttpConnection(): self
    {
        return new self(
            'Authentication supports only HTTPS if not served via PHP web server.'
        );
    }
}
