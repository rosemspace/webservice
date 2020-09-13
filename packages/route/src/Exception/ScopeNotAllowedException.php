<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Exception;

use Rosem\Component\Route\AllowedScopeTrait;
use RuntimeException;
use Throwable;

use function implode;
use function sprintf;

class ScopeNotAllowedException extends RuntimeException
{
    use AllowedScopeTrait;

    public function __construct(
        array $allowedScopes,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->setAllowedScopes($allowedScopes);

        parent::__construct($message, $code, $previous);
    }

    public static function forNotAllowedScopes(array $scopes, array $allowedScopes = []): self
    {
        return new self(
            $allowedScopes,
            sprintf(
                'The following scopes are not allowed: "%s", use "%s" instead.',
                implode('", "', $scopes),
                implode('", "', $allowedScopes),
            )
        );
    }
}
