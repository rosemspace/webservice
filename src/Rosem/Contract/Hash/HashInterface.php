<?php

namespace Rosem\Contract\Hash;

/**
 * Representation of hasher.
 */
interface HashInterface
{
    /**
     * Hash the given value.
     *
     * @param  string $value
     * @param  array  $options
     *
     * @return string
     * @throws \RuntimeException
     */
    public function hash(string $value, array $options = []) : string;

    /**
     * Check the given plain value against a hash.
     *
     * @param  string $value
     * @param  string $hashedValue
     *
     * @return bool
     */
    public function verify(string $value, string $hashedValue) : bool;

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string $hashedValue
     * @param  array  $options
     *
     * @return bool
     */
    public function needsRehash(string $hashedValue, array $options = []) : bool;

    /**
     * Get information about the given hashed value.
     *
     * @param  string $hashedValue
     *
     * @return array
     */
    public function getInfo(string $hashedValue) : array;
}
