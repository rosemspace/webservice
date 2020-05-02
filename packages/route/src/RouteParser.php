<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use InvalidArgumentException;
use Rosem\Component\Route\Contract\RouteParserInterface;
use Rosem\Component\Route\Exception\BadRouteException;

use function preg_quote;
use function preg_replace;
use function preg_replace_callback;
use function strlen;

class RouteParser implements RouteParserInterface
{
    public const REGEXP_DELIMITER = '~';

    protected bool $utf8 = false;

    protected string $delimiter = '/';

    protected array $variableTokens = ['{', '}'];

    protected string $variableRegExpToken = ':';

    protected string $defaultDispatchRegex;

    protected string $variableSplitRegex;

    public function __construct(array $config = [])
    {
        //@TODO RouteParserConfig class
        $this->utf8 ??= $config['utf8'];
        $this->delimiter ??= $config['delimiter'];
        $this->variableTokens ??= (array)$config['variableTokens'];
        $this->variableRegExpToken ??= $config['variableRegExpToken'];
        $this->defaultDispatchRegex = $config['dispatchRegExp'] ?? "[^$this->delimiter]++";
        $nameRegExp = $this->utf8 ? '[[:alpha:]_][[:alnum:]_-]*' : '[a-zA-Z_][a-zA-Z0-9_-]*';
        // (?<!\\\\){\s*(?P<name>[a-zA-Z_][a-zA-Z0-9_-]*)\s*:?(?P<regExp>[^\/]*?[^{]*)(?<!\\\\)}
        $this->variableSplitRegex = self::REGEXP_DELIMITER .
            self::escape(
                <<<REGEXP
                (?<!\\\\){$this->variableTokens[0]}\s*
                (?P<name>$nameRegExp)?\s*$this->variableRegExpToken?
                (?P<regExp>[^/]*?[^{$this->variableTokens[0]}]*)
                REGEXP,
                self::REGEXP_DELIMITER
            );

        if (isset($this->variableTokens[1])) {
            $this->variableSplitRegex .= self::escape(
                '(?<!\\\\)' . $this->variableTokens[1],
                self::REGEXP_DELIMITER
            );
        }

        $this->variableSplitRegex .= self::REGEXP_DELIMITER . 'x';

        if ($this->utf8) {
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
        if ($routePattern === '') {
            throw BadRouteException::forEmptyRoute();
        }

        $variableNames = [];
        $index = 0;
        $defaultDispatchRegex = $this->defaultDispatchRegex;
        // TODO: parse protocol
        // TODO: parse host
        // TODO: parse optional end part
        $regExp = preg_replace_callback(
            $this->variableSplitRegex,
            static function ($matches) use (&$variableNames, &$index, &$defaultDispatchRegex) {
                $variableName = $matches['name'] ?: $index;
                $variableNames[] = $variableName;
                ++$index;

                // @TODO: parse user groups in $matches[2] regex
                if (empty($matches['regExp'])) {
                    $variableRegExp = $defaultDispatchRegex;
                } else {
                    $variableRegExp = self::escape($matches[2], self::REGEXP_DELIMITER);

                    if (!Regex::isValid(self::REGEXP_DELIMITER . $variableRegExp . self::REGEXP_DELIMITER)) {
                        throw BadRouteException::dueToInvalidVariableRegExp($matches[2], $variableName);
                    }
                }

                return "($variableRegExp)";
            },
            $routePattern
        );

        return [
            [self::escape($regExp, self::REGEXP_DELIMITER), $variableNames],
        ];
    }

    private static function escape(string $regExp, string $delimiter): string
    {
        if (strlen($delimiter) > 1) {
            throw new InvalidArgumentException(
                "Provided \"$delimiter\" is too long. Please use only 1 character delimiter."
            );
        }

        $escapedDelimiter = preg_quote($delimiter, '/');

        return preg_replace("/(?<!\\\\)$escapedDelimiter/", "\\$delimiter", $regExp);
    }
}
