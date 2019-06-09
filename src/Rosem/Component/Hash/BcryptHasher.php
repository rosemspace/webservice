<?php

namespace Rosem\Component\HashManager;

use RuntimeException;

class BcryptHasher extends AbstractHasher
{
    /**
     * The default cost factor.
     *
     * @var int
     */
    protected $cost = PASSWORD_BCRYPT_DEFAULT_COST;

    /**
     * BcryptHasher constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = $this->mergeOptions($options);
        $this->setCost($options['cost']);
    }

    /**
     * Merge given options with default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function mergeOptions(array $options = []) : array
    {
        return [
            'cost' => $options['cost'] ?? $this->cost,
        ];
    }

    /**
     * Hash the given value.
     *
     * @param  string $value
     * @param  array  $options
     *
     * @return string
     * @throws RuntimeException
     */
    public function hash(string $value, array $options = []) : string
    {
        $hash = password_hash($value, PASSWORD_BCRYPT, $this->mergeOptions($options));

        if ($hash === false) {
            throw new RuntimeException('Bcrypt hashing not supported.');
        }

        return $hash;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string $hashedValue
     * @param  array  $options
     *
     * @return bool
     */
    public function needsRehash(string $hashedValue, array $options = []) : bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, $this->mergeOptions($options));
    }

    /**
     * Set the default password work factor.
     *
     * @param  int $cost
     *
     * @return void
     */
    public function setCost(int $cost) : void
    {
        $this->cost = $cost;
    }
}
