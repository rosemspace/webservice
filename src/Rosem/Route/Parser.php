<?php

namespace Rosem\Route;

class Parser implements ParserInterface
{
    protected const VARIABLE_TOKENS = ['{', '}'];

    protected const VARIABLE_REGEX_TOKEN = ':';

    protected const DEFAULT_DISPATCH_REGEX = '[^/]++';

    private $variableSplitRegex;

    public function __construct(bool $useUtf8 = false)
    {
        // {\s*([a-zA-Z_][a-zA-Z0-9_-]*)\s*:?([^\/]*?[^{]*)}
        $this->variableSplitRegex = '/'
            . static::VARIABLE_TOKENS[0]
            . '\s*([[:alpha:]_][[:alnum:]_-]*)\s*' . static::VARIABLE_REGEX_TOKEN
            . '?([^\\/]*?[^' . static::VARIABLE_TOKENS[0] . ']*)'
            . static::VARIABLE_TOKENS[1]
            . '/';

        if ($useUtf8) {
            $this->variableSplitRegex .= 'u';
        }
    }

    /**
     * @param string $routePattern
     *
     * @return array[]
     */
    public function parse(string $routePattern): array // TODO: parse user groups and optional end part
    {
        $variableNames = [];
        $index = 0;
        $regex = preg_replace_callback($this->variableSplitRegex, function ($matches) use (&$variableNames, &$index) {
            $variableNames[] = $matches[1] ?: $index;
            ++$index;

            return '(' . ($matches[2] ?: self::DEFAULT_DISPATCH_REGEX) . ')';
        }, $routePattern);

        return [
            [$regex, $variableNames],
        ];
    }
}
