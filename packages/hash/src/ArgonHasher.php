<?php

declare(strict_types=1);

namespace Rosem\Component\Hash;

use RuntimeException;

class ArgonHasher extends AbstractHasher
{
    /**
     * Default threads factor.
     */
    protected int $threads = PASSWORD_ARGON2_DEFAULT_THREADS;

    /**
     * Default memory cost factor.
     */
    protected int $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST;

    /**
     * Default time cost factor.
     */
    protected int $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST;

    /**
     * ArgonHasher constructor.
     */
    public function __construct(array $options = [])
    {
        $options = $this->mergeOptions($options);
        $this->setMemoryCost($options['memory_cost']);
        $this->setTimeCost($options['time_cost']);
        $this->setThreads($options['threads']);
    }

    public function hash(string $value, array $options = []): string
    {
        $hash = password_hash($value, PASSWORD_ARGON2I, $this->mergeOptions($options));

        if ($hash === false) {
            throw new RuntimeException('Argon2 hashing not supported.');
        }

        return $hash;
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_ARGON2I, $this->mergeOptions($options));
    }

    /**
     * Set the default password threads factor.
     */
    public function setThreads(int $threads): void
    {
        $this->threads = $threads;
    }

    /**
     * Set the default password memory factor.
     */
    public function setMemoryCost(int $memoryCost): void
    {
        $this->memoryCost = $memoryCost;
    }

    /**
     * Set the default password timing factor.
     */
    public function setTimeCost(int $timeCost): void
    {
        $this->timeCost = $timeCost;
    }

    /**
     * Merge given options with default options.
     */
    protected function mergeOptions(array $options = []): array
    {
        return [
            'memory_cost' => $options['memory_cost'] ?? $this->memoryCost,
            'time_cost' => $options['time_cost'] ?? $this->timeCost,
            'threads' => $options['threads'] ?? $this->threads,
        ];
    }
}
