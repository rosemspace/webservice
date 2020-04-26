<?php

namespace Rosem\Component\Hash;

use Rosem\Utility\String\Str;

class Sha256Hasher extends AbstractHasher
{
    /**
     * The default length of a random salt string.
     */
    public const SALT_DEFAULT_LENGTH = 32;

    /**
     * The delimiter character for a salt string.
     */
    public const SALT_DEFAULT_DELIMITER = ':';

    /**
     * The salt string which will be prepended to a value which should be hashed.
     *
     * @var string
     */
    protected string $salt = '';

    /**
     * Merge given options with default options.
     *
     * @param array $options
     *
     * @return array
     * @throws \Exception
     */
    protected function mergeOptions(array $options = []): array
    {
        return ['salt' => $options['salt'] ?? self::SALT_DEFAULT_LENGTH];
    }

    /**
     * @inheritDoc
     */
    public function hash(string $value, array $options = []): string
    {
        $hash = hash('sha256', $this->salt . $value, $this->mergeOptions($options)['rawOutput']);

        return empty($this->salt) ? $hash : $hash . self::SALT_DEFAULT_DELIMITER . $this->salt;
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
//        explode(self::SALT_DEFAULT_DELIMITER, $hashedValue, 2)[0],
    }

    /**
     * @inheritDoc
     */
    public function verify(string $value, string $hashedValue): bool
    {
        if ('' === $hashedValue) {
            return false;
        }

        [$hash, $salt] = explode(self::SALT_DEFAULT_DELIMITER, $hashedValue, 2);

        //todo
        return password_verify("$salt$value", $hash);
    }

    /**
     * Set the default salt string or the length of a random salt string.
     *
     * @param string|int $salt
     *
     * @return void
     * @throws \Exception
     */
    public function setSalt($salt): void
    {
        if (empty($salt)) {
            return;
        }

        if (is_numeric($salt)) {
            $salt = Str::random((int)$salt);
        } elseif (!is_string($salt)) {
            $salt = Str::random(self::SALT_DEFAULT_LENGTH);
        }

        $this->salt = $salt;
    }
}
