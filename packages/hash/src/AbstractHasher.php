<?php

namespace Rosem\Component\Hash;

use Rosem\Contract\Hash\HasherInterface;

abstract class AbstractHasher implements HasherInterface
{
    /**
     * @inheritDoc
     */
    public function verify(string $value, string $hashedValue): bool
    {
        return '' === $hashedValue ? false : password_verify($value, $hashedValue);
    }

    /**
     * Get information about the given hashed value.
     *
     * @param string $hashedValue
     *
     * @return array
     */
    public function getInfo(string $hashedValue): array
    {
        return password_get_info($hashedValue);
    }
}
