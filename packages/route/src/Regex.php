<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use RuntimeException;

use function preg_last_error;
use function preg_match;
use function strlen;
use function strpos;
use function substr_replace;

use const PREG_BACKTRACK_LIMIT_ERROR;
use const PREG_BAD_UTF8_ERROR;
use const PREG_BAD_UTF8_OFFSET_ERROR;
use const PREG_INTERNAL_ERROR;
use const PREG_JIT_STACKLIMIT_ERROR;
use const PREG_NO_ERROR;
use const PREG_RECURSION_LIMIT_ERROR;

class Regex
{
    /**
     * A regular expression to check if other regular expression has capturing groups.
     * () - yes
     * \() - invalid
     * \(\) - no
     * (\) - invalid
     * (a) - yes
     * (a|b) - yes
     * (\?a) - yes
     * \(\?a\) - no
     * (?:a) - no
     * (?:?a) - invalid
     * (?:\?a) - no
     * (?>a) - no
     * (?|a) - no
     * (?#a) - no
     * (?'a'b) - yes, where "a" should match [a-zA-Z_][a-zA-Z0-9_]*
     * (?<a>b) - yes
     * (?P<a>b) - yes
     * (?ia) - no
     * (?-ia) - no
     * (?(1)a|b) - invalid, requires Nth capturing group. Ex: (?(2)a|b)()()
     * (?(R)a|b) - no
     * (?(R1)a|b) - NA+
     * (?(R&name)a|b) - NA+
     * (?(?=is)a|b) - no
     * (?(?<=is)a|b) - no
     * (?R) - no
     * (?1) - NA
     * (?+1) - NA
     * (?&name) - invalid, requires "name" capturing group. Ex: (?&name)(?<name>)
     * (?P=name) - NA
     * (?P>name) - NA
     * (?(DEFINE)(?<a>)b) - FX
     * ((?P>a)) - yes
     * (?=a) - no
     * (?!a) - no
     * (?<=a) - no
     * (?<!a) - no
     * (*ACCEPT) - no
     * (*SKIP) - no
     * (*FAIL) - no
     * (*MARK) - no
     */
    public const REGEX_GROUP = <<<'REGEXP'
        ~ (?:
            \(\?\(|\[[^]\\\\]*(?:
                \\\\.[^]\\\\]*
            )*]|\\\\.
        )(*SKIP)(*FAIL)
        | (?<!
            \(\?\(DEFINE\)
        )\((?!
            \?(?!
                <(?![!=])
                |P<
                |'
            )
            |\*
        ) ~x
        REGEXP;

    /**
     * @see https://www.php.net/manual/ru/function.preg-last-error.php
     * @see https://www.php.net/manual/ru/pcre.constants.php
     * @see https://github.com/php/php-src/blob/master/ext/pcre/php_pcre.c
     */
    public const PREG_ERROR_MESSAGES = [
        PREG_NO_ERROR => 'No error',
        PREG_INTERNAL_ERROR => 'Internal error',
        PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit exhausted',
        PREG_RECURSION_LIMIT_ERROR => 'Recursion limit exhausted',
        PREG_BAD_UTF8_ERROR => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        PREG_BAD_UTF8_OFFSET_ERROR => 'The offset did not correspond to the beginning of a valid UTF-8 code point',
        PREG_JIT_STACKLIMIT_ERROR => 'JIT stack limit exhausted',
    ];

    /**
     * The regular expression.
     *
     * @var string
     */
    protected string $regexp;

    /**
     * A flag to check if the regular expression has capturing groups.
     *
     * @var bool|null
     */
    private ?bool $hasCapturingGroups = null;

    /**
     * Regex constructor.
     *
     * @param string $regexp
     *
     * @throws RuntimeException
     */
    public function __construct(string $regexp)
    {
        self::assertValid($regexp);

        $this->regexp = $regexp;
    }

    /**
     * Static constructor.
     *
     * @param string $regexp
     *
     * @return static
     */
    public static function of(string $regexp): self
    {
        return new self($regexp);
    }

    /**
     * Check if the regular expression is valid.
     *
     * @param string $regexp
     *
     * @return bool
     */
    public static function isValid(string $regexp): bool
    {
        return @preg_match($regexp, '') !== false;
    }

    /**
     * Throw an exception if the regular expression is not valid.
     *
     * @param string $regexp
     *
     * @return void
     * @trows RuntimeException
     */
    public static function assertValid(string $regexp): void
    {
        if (self::isValid($regexp)) {
            return;
        }

        $errorCode = self::getLastErrorCode();

        throw new RuntimeException(self::PREG_ERROR_MESSAGES[$errorCode], $errorCode);
    }

    /**
     * Retrieve a code of the last error occurred.
     *
     * @return int
     */
    public static function getLastErrorCode(): int
    {
        return preg_last_error();
    }

    /**
     * Retrieve a message of the last error occurred.
     *
     * @return string
     */
    public static function getLastErrorMessage(): string
    {
        return self::PREG_ERROR_MESSAGES[self::getLastErrorCode()] ?? 'Unknown error';
    }

    /**
     * Check if the regular expression has capturing groups.
     *
     * @return bool
     */
    public function hasCapturingGroups(): bool
    {
        if ($this->hasCapturingGroups !== null) {
            return $this->hasCapturingGroups;
        }

        if (strpos($this->regexp, '(') === false) {
            // Needs to have at least a ( to contain a capturing group
            $this->hasCapturingGroups = false;
        } else {
            $this->hasCapturingGroups = (bool)preg_match(self::REGEX_GROUP, $this->regexp);
        }

        return $this->hasCapturingGroups;
    }

    public function disableCapturingGroups(): void
    {
        $regexp = $this->regexp;
        $regexpLength = strlen($regexp);

        for ($i = 0; $i < $regexpLength; ++$i) {
            if ($regexp[$i] === '\\') {
                ++$i;
                continue;
            }

            if ('(' !== $regexp[$i] || !isset($regexp[$i + 2])) {
                continue;
            }

            if ('*' === $regexp[++$i] || '?' === $regexp[$i]) {
                ++$i;
                continue;
            }

            $regexp = substr_replace($regexp, '?:', $i, 0);
            ++$i;
        }

        $this->regexp = $regexp;
    }
}
