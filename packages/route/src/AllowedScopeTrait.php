<?php

namespace Rosem\Component\Route;

use Rosem\Component\Route\Exception\ScopeNotAllowedException;

use function array_diff;
use function array_map;
use function mb_strtoupper;

trait AllowedScopeTrait
{
    /**
     * Allowed scopes.
     *
     * @var array
     */
    protected array $allowedScopes = [];

    public static function normalizeScopes(array $scopes): array
    {
        return array_map('mb_strtoupper', $scopes);
    }

    protected function setAllowedScopes(array $allowedScopes): void
    {
        $this->allowedScopes = self::normalizeScopes($allowedScopes);
    }

    /**
     * Gets the allowed scopes.
     *
     * @return string[]
     */
    public function getAllowedScopes(): array
    {
        return $this->allowedScopes;
    }

    /**
     * Check if scopes are allowed.
     *
     * @param array $scopes
     * @throws ScopeNotAllowedException
     */
    protected function assertAllowedScopes(array $scopes): void
    {
        $notAllowedScopes = array_diff($scopes, $this->allowedScopes);

        if (count($notAllowedScopes) > 0) {
            throw ScopeNotAllowedException::forNotAllowedScopes($notAllowedScopes, $this->allowedScopes);
        }
    }
}
