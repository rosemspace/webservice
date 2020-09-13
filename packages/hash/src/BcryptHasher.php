<?php

declare(strict_types=1);

namespace Rosem\Component\Hash;

use LengthException;
use RuntimeException;

use function mb_strlen;

class BcryptHasher extends AbstractHasher
{
    /**
     * Max length of string to hash.
     */
    public const VALUE_MAX_LENGTH = 72;

    /**
     * Hash string length.
     */
    public const HASH_LENGTH = 60;

    /**
     * The default cost factor.
     */
    protected int $cost = PASSWORD_BCRYPT_DEFAULT_COST;

    /**
     * BcryptHasher constructor.
     */
    public function __construct(array $options = [])
    {
        $options = $this->mergeOptions($options);
        $this->setCost($options['cost']);
    }

    public function hash(string $value, array $options = []): string
    {
        $this->assertValid($value);
        $hash = password_hash($value, PASSWORD_BCRYPT, $this->mergeOptions($options));

        if ($hash === false) {
            throw new RuntimeException('Crypt Blowfish hashing not supported.');
        }

        return $hash;
    }

    public function verify(string $value, string $hashedValue): bool
    {
        $this->assertValid($value);

        return parent::verify($value, $hashedValue);
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, $this->mergeOptions($options));
    }

    /**
     * Check if the given value is not too long.
     *
     * @see https://www.php.net/manual/ru/function.password-hash.php
     */
    public function validate(string $value): bool
    {
        return mb_strlen($value) <= self::VALUE_MAX_LENGTH;
    }

    /**
     * Set the default password work factor.
     */
    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * Merge given options with default options.
     */
    protected function mergeOptions(array $options = []): array
    {
        return [
            'cost' => $options['cost'] ?? $this->cost,
        ];
    }

    /**
     * Throw an exception if the given value is too long.
     */
    private function assertValid(string $value): void
    {
        if (! $this->validate($value)) {
            throw new LengthException("Value \"${value}\" is too long for bcrypt hashing.");
        }
    }
}
