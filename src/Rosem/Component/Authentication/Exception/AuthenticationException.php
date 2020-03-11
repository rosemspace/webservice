<?php

namespace Rosem\Component\Authentication\Exception;

use Exception;
use Rosem\Contract\Authentication\AuthenticationExceptionInterface;

class AuthenticationException extends Exception implements AuthenticationExceptionInterface
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
