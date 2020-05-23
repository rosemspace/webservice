<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use InvalidArgumentException;
use RuntimeException;

use function preg_last_error;
use function preg_match;
use function preg_replace;
use function str_pad;
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
    protected string $regex;

    /**
     * A flag to check if the regular expression has capturing groups.
     *
     * @var bool|null
     */
    private ?bool $hasCapturingGroups = null;

    /**
     * Regex constructor.
     *
     * @param string $regex
     *
     * @throws RuntimeException
     */
    public function __construct(string $regex)
    {
        self::assertValid($regex);

        $this->regex = $regex;
    }

    /**
     * Static constructor.
     *
     * @param string $regex
     *
     * @return static
     */
    public static function of(string $regex): self
    {
        return new self($regex);
    }

    /**
     * Check if the regular expression is valid.
     *
     * @param string $regex
     *
     * @return bool
     */
    public static function isValid(string $regex): bool
    {
        return @preg_match($regex, '') !== false;
    }

    /**
     * Throw an exception if the regular expression is not valid.
     *
     * @param string $regex
     *
     * @return void
     * @trows RuntimeException
     */
    public static function assertValid(string $regex): void
    {
        if (self::isValid($regex)) {
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

    public static function escapeDelimiters(string $regex, string $delimiters): string
    {
        if ($delimiters === '\\') {
            throw new InvalidArgumentException(
                "Provided delimiter \"$delimiters\" is not allowed"
            );
        }

        $delimitersLength = strlen($delimiters);

        if ($delimitersLength > 1) {
            throw new InvalidArgumentException(
                "Provided delimiters \"$delimiters\" is too long. Please use 2 characters maximum delimiter"
            );
        }

        if ($delimitersLength === 2) {
            // @TODO escape asymmetric {} [] ()
        }

        $escapedDelimiter = preg_quote($delimiters, '/');

        return preg_replace("/(?<!\\\\)$escapedDelimiter/", "\\$delimiters", $regex);
    }

    public static function wrapWithDelimiters(string $regex, string $delimiters): string
    {
        $delimiters2 = str_pad($delimiters, 2, $delimiters[0]);

        return $delimiters2[0] . self::escapeDelimiters($regex, $delimiters) . $delimiters2[1];
    }

    /**
     * Tests whether this regex matches the given string.
     *
     * @param string $string
     *
     * @return bool
     */
    public function matches(string $string): bool
    {
        return (bool) preg_match($this->regex, $string);
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

        if (strpos($this->regex, '(') === false) {
            // Needs to have at least a ( to contain a capturing group
            $this->hasCapturingGroups = false;
        } else {
            $this->hasCapturingGroups = (bool)preg_match(self::REGEX_GROUP, $this->regex);
        }

        return $this->hasCapturingGroups;
    }

    public function disableCapturingGroups(): void
    {
        $regex = $this->regex;
        $regexLength = strlen($regex);

        for ($i = 0; $i < $regexLength; ++$i) {
            if ($regex[$i] === '\\') {
                ++$i;
                continue;
            }

            if ('(' !== $regex[$i] || !isset($regex[$i + 2])) {
                continue;
            }

            if ('*' === $regex[++$i] || '?' === $regex[$i]) {
                ++$i;
                continue;
            }

            $regex = substr_replace($regex, '?:', $i, 0);
            ++$i;
        }

        $this->regex = $regex;
    }
}
