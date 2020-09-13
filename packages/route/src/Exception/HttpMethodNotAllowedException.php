<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Exception;

use function implode;
use function sprintf;

final class HttpMethodNotAllowedException extends ScopeNotAllowedException
{
    public function getAllowedHttpMethods(): array
    {
        return $this->getAllowedScopes();
    }

    public static function forNotAllowedHttpMethods(array $scopes, array $allowedScopes = []): self
    {
        return new self(
            $allowedScopes,
            sprintf(
                'The following methods are not allowed: "%s", use "%s" instead.',
                implode('", "', $scopes),
                implode('", "', $allowedScopes),
            )
        );
    }
}
