<?php

declare(strict_types=1);

namespace Rosem\Contract\Hash;

use Exception;
use RuntimeException;

/**
 * Representation of hasher.
 */
interface HasherInterface
{
    /**
     * Hash the given value.
     *
     * @throws RuntimeException
     * @throws Exception
     */
    public function hash(string $value, array $options = []): string;

    /**
     * Check the given plain value against a hash.
     */
    public function verify(string $value, string $hashedValue): bool;

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @throws Exception
     */
    public function needsRehash(string $hashedValue, array $options = []): bool;

    /**
     * Get information about the given hashed value.
     */
    public function getInfo(string $hashedValue): array;
}
