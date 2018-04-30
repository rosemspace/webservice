<?php

namespace Rosem\HashManager;

use Psrnext\Hash\HasherInterface;

abstract class AbstractHasher implements HasherInterface
{
    /**
     * Check the given plain value against a hash.
     *
     * @param  string $value
     * @param  string $hashedValue
     *
     * @return bool
     */
    public function verify(string $value, string $hashedValue) : bool
    {
        return '' === $hashedValue ? false : password_verify($value, $hashedValue);
    }

    /**
     * Get information about the given hashed value.
     *
     * @param  string $hashedValue
     *
     * @return array
     */
    public function getInfo(string $hashedValue) : array
    {
        return password_get_info($hashedValue);
    }
}
