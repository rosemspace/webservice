<?php

namespace Rosem\Component\Route;

class Regex
{
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
     */
    public const PREG_ERROR_MESSAGES = [
        PREG_NO_ERROR => 'No errors',
        PREG_INTERNAL_ERROR => 'There was an internal PCRE error',
        PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit was exhausted',
        PREG_RECURSION_LIMIT_ERROR => 'Recursion limit was exhausted',
        PREG_BAD_UTF8_ERROR => 'The offset did not correspond to the begin of a valid UTF-8 code point',
        PREG_BAD_UTF8_OFFSET_ERROR => 'Malformed UTF-8 data',
        PREG_JIT_STACKLIMIT_ERROR => 'Limited JIT stack space',
    ];

    /**
     * @var string
     */
    protected string $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    public static function from(string $regex): self
    {
        return new Regex($regex);
    }

    protected static function getLastErrorMessage(): string
    {
        return self::PREG_ERROR_MESSAGES[preg_last_error()];
    }

    public function isValid(): bool
    {
        return @preg_match($this->regex, null) !== false;
    }

    public function hasCapturingGroups(): bool
    {
        return $this->isValid() && (bool) preg_match(self::REGEX_GROUP, $this->regex);
    }
}
