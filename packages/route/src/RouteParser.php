<?php

namespace Rosem\Component\Route;

use Rosem\Component\Route\Contract\RouteParserInterface;

class RouteParser implements RouteParserInterface
{
    public const KEY_REGEX = 0;

    public const KEY_VARIABLES_NAMES = 1;

    protected const DEFAULT_VARIABLE_TOKENS = ['{', '}'];

    protected const DEFAULT_VARIABLE_REGEX_TOKEN = ':';

    protected const DEFAULT_DISPATCH_REGEX = '[^/]+';

    protected string $variableSplitRegex;

    public function __construct(bool $useUtf8 = false)
    {
        $nameRegExp = $useUtf8 ? '[[:alpha:]_][[:alnum:]_]*' : '[a-zA-Z_][a-zA-Z0-9_]*';
        // (?<!\\\\){\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*:?([^\/]*?[^{]*)(?<!\\\\)}
        $this->variableSplitRegex = '~'
            . '(?<!\\\\)' . static::DEFAULT_VARIABLE_TOKENS[0]
            . "\s*($nameRegExp)\s*" . static::DEFAULT_VARIABLE_REGEX_TOKEN
            . '?([^\\/]*?[^' . static::DEFAULT_VARIABLE_TOKENS[0] . ']*)'
            . '(?<!\\\\)' . static::DEFAULT_VARIABLE_TOKENS[1]
            . '~'; //x;

        if ($useUtf8) {
            $this->variableSplitRegex .= 'u';
        }
    }

    /**
     * @param string $routePattern
     *
     * @return array[]
     */
    public function parse(string $routePattern): array
    {
        $variableNames = [];
        $index = 0;
        // TODO: parse user groups
        // TODO: parse optional end part
        $regex = preg_replace_callback(
            $this->variableSplitRegex,
            static function ($matches) use (&$variableNames, &$index) {
                $variableNames[] = $matches[1] ?: $index;
                ++$index;

                return '(' . ($matches[2] ?: self::DEFAULT_DISPATCH_REGEX) . ')';
            },
            $routePattern
        );

        return [
            [
                self::KEY_REGEX => $regex,
                self::KEY_VARIABLES_NAMES => $variableNames,
            ],
        ];
    }
}
