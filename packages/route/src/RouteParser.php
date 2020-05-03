<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use InvalidArgumentException;
use Rosem\Component\Route\Contract\RouteParserInterface;
use Rosem\Component\Route\Exception\BadRouteException;

use function preg_match;
use function preg_quote;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function strlen;

class RouteParser implements RouteParserInterface
{
    public const REGEXP_DELIMITER = '~';

    protected bool $utf8 = false;

    protected string $delimiter = '/';

    protected array $variableTokens = ['{', '}'];

    protected string $variableRegExpToken = ':';

    protected array $optionalSegmentTokens = ['[', ']'];

    protected string $defaultDispatchRegExp;

    protected string $variableSegmentRegExp;

    protected string $optionalSegmentOpenRegExp;

    protected string $optionalSegmentCloseRegExp;

    public function __construct(array $config = [])
    {
        //@TODO RouteParserConfig class
        $this->utf8 ??= $config['utf8'];
        $this->delimiter ??= $config['delimiter'];
        $this->variableTokens ??= (array)$config['variableTokens'];
        $this->variableRegExpToken ??= $config['variableRegExpToken'];
        $this->optionalSegmentTokens ??= $config['optionalSegmentTokens'];
        $this->defaultDispatchRegExp = $config['dispatchRegExp'] ?? "[^$this->delimiter]++";
        $nameRegExp = $this->utf8 ? '[[:alpha:]_][[:alnum:]_-]*' : '[a-zA-Z_][a-zA-Z0-9_-]*';
        // (?<!\\\\){\s*(?P<name>[a-zA-Z_][a-zA-Z0-9_-]*)\s*:?(?P<regExp>[^\/]*?[^{]*)(?<!\\\\)}
        $this->variableSegmentRegExp = self::REGEXP_DELIMITER .
            self::escape(
                <<<REGEXP
                (?<!\\\\){$this->variableTokens[0]}\s*
                (?P<name>$nameRegExp)?\s*$this->variableRegExpToken?
                (?P<regExp>[^/]*?[^{$this->variableTokens[0]}]*)
                REGEXP,
                self::REGEXP_DELIMITER
            );

        if (isset($this->variableTokens[1])) {
            $this->variableSegmentRegExp .= self::escape(
                '(?<!\\\\)' . $this->variableTokens[1],
                self::REGEXP_DELIMITER
            );
        }

        $regExpPart = $this->variableSegmentRegExp . self::escape('(*SKIP)(*F)|', self::REGEXP_DELIMITER);
        $this->optionalSegmentOpenRegExp = $regExpPart .
            preg_quote($this->optionalSegmentTokens[0], self::REGEXP_DELIMITER);
        $this->optionalSegmentCloseRegExp = $regExpPart .
            preg_quote($this->optionalSegmentTokens[1], self::REGEXP_DELIMITER);
        $regExpPart = self::REGEXP_DELIMITER . 'x';
        $this->variableSegmentRegExp .= $regExpPart;
        $this->optionalSegmentOpenRegExp .= $regExpPart;
        $this->optionalSegmentCloseRegExp .= $regExpPart;

        if ($this->utf8) {
            $regExpPart = 'u';
            $this->variableSegmentRegExp .= $regExpPart;
            $this->optionalSegmentOpenRegExp .= $regExpPart;
            $this->optionalSegmentCloseRegExp .= $regExpPart;
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

        $segments = $this->parseOptionalSegments($routePattern);
        $routeDataList = [];
        $currentRoute = '';

        foreach ($segments as $index => $segment) {
            if ($index !== 0 && $segment === '') {
                throw BadRouteException::dueToEmptyOptionalSegment();
            }

            //@TODO config for delimiter
            $currentRoute .= $segment;// ?: $this->delimiter;
            $routeDataList[] = $this->parseVariableSegments($currentRoute);
        }

        return $routeDataList;
    }

    protected function parseOptionalSegments(string $routePattern): array
    {
        $routePatternWithoutClosingOptionals = rtrim($routePattern, $this->optionalSegmentTokens[1]);
        $optionalSegmentCount = strlen($routePattern) - strlen($routePatternWithoutClosingOptionals);
        // Split on "[" while skipping variable segments
        $segments = preg_split($this->optionalSegmentOpenRegExp, $routePatternWithoutClosingOptionals);

        if ($optionalSegmentCount !== count($segments) - 1) {
            // If there are any "]" in the middle of the route, throw a more specific error message
            if (preg_match($this->optionalSegmentCloseRegExp, $routePatternWithoutClosingOptionals)) {
                throw BadRouteException::dueToWrongOptionalSegmentPosition();
            }

            throw BadRouteException::dueToWrongOptionalSegmentPair(
                $this->optionalSegmentTokens[0],
                $this->optionalSegmentTokens[1],
            );
        }

        return $segments;
    }

    protected function parseVariableSegments(string $routePattern): array
    {
        $variableNames = [];
        $index = 0;
        // TODO: parse protocol
        // TODO: parse host
        // TODO: parse optional end part
        $regExp = preg_replace_callback(
            $this->variableSegmentRegExp,
            function ($matches) use (&$variableNames, &$index) {
                $variableName = $matches['name'] ?: $index;
                $variableNames[] = $variableName;
                ++$index;

                // @TODO: parse user groups in $matches[2] regex
                if (empty($matches['regExp'])) {
                    $variableRegExp = $this->defaultDispatchRegExp;
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

        return [self::escape($regExp, self::REGEXP_DELIMITER), $variableNames];
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
