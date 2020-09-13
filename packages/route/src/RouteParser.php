<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use Rosem\Component\Route\Contract\RouteParserInterface;
use Rosem\Component\Route\Exception\BadRouteException;

use function preg_match;
use function preg_quote;
use function preg_replace_callback;
use function preg_split;
use function strlen;

class RouteParser implements RouteParserInterface
{
    public const REGEX_DELIMITER = '~';

    protected bool $utf8 = false;

    protected string $delimiter = '/';

    protected array $variableTokens = ['{', '}'];

    protected string $variableRegexToken = ':';

    protected array $optionalSegmentTokens = ['[', ']'];

    protected string $defaultDispatchRegex;

    protected string $variableSegmentRegex;

    protected string $optionalSegmentOpenRegex;

    protected string $optionalSegmentCloseRegex;

    public function __construct(array $config = [])
    {
        //@TODO RouteParserBuilder class
        $this->utf8 ??= $config['utf8'];
        $this->delimiter ??= $config['delimiter'];
        $this->variableTokens ??= (array) $config['variableTokens'];
        $this->variableRegexToken ??= $config['variableRegexToken'];
        $this->optionalSegmentTokens ??= $config['optionalSegmentTokens'];
        $this->defaultDispatchRegex = $config['dispatchRegex'] ?? "[^{$this->delimiter}]++";
        $nameRegex = $this->utf8 ? '[[:alpha:]_][[:alnum:]_-]*' : '[a-zA-Z_][a-zA-Z0-9_-]*';
        // ~(?<!\\){\s*(?P<name>[a-zA-Z_][a-zA-Z0-9_-]*)?\s*:?(?P<regex>.*?(?:[^{]|\\{)*)(?<!\\)}~sx
        $this->variableSegmentRegex = self::REGEX_DELIMITER .
            Regex::escapeDelimiters(
                <<<REGEXP
                (?<!\\\\){$this->variableTokens[0]}\s*
                (?P<name>${nameRegex})?\s*{$this->variableRegexToken}?
                (?P<regex>.*?(?:[^{$this->variableTokens[0]}]|\\\\{$this->variableTokens[0]})*)
                REGEXP,
                self::REGEX_DELIMITER
            );

        if (isset($this->variableTokens[1])) {
            $this->variableSegmentRegex .= Regex::escapeDelimiters(
                "(?<!\\\\){$this->variableTokens[1]}",
                self::REGEX_DELIMITER
            );
        }

        $regexPart = $this->variableSegmentRegex .
            Regex::escapeDelimiters('(*SKIP)(*F)|', self::REGEX_DELIMITER);
        $this->optionalSegmentOpenRegex = $regexPart .
            preg_quote($this->optionalSegmentTokens[0], self::REGEX_DELIMITER);
        $this->optionalSegmentCloseRegex = $regexPart .
            preg_quote($this->optionalSegmentTokens[1], self::REGEX_DELIMITER);
        $regexPart = self::REGEX_DELIMITER . 'sx';
        $this->variableSegmentRegex .= $regexPart;
        $this->optionalSegmentOpenRegex .= $regexPart;
        $this->optionalSegmentCloseRegex .= $regexPart;

        if ($this->utf8) {
            $regexPart = 'u';
            $this->variableSegmentRegex .= $regexPart;
            $this->optionalSegmentOpenRegex .= $regexPart;
            $this->optionalSegmentCloseRegex .= $regexPart;
        }
    }

    /**
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

            //@TODO config for delimiter (?: delimiter)
            $currentRoute .= $segment;
            $routeDataList[] = $this->parseVariableSegments($currentRoute);
        }

        return $routeDataList;
    }

    protected function parseOptionalSegments(string $routePattern): array
    {
        $routePatternWithoutClosingOptionals = rtrim($routePattern, $this->optionalSegmentTokens[1]);
        $optionalSegmentCount = strlen($routePattern) - strlen($routePatternWithoutClosingOptionals);
        // Split on "[" while skipping variable segments
        $segments = preg_split($this->optionalSegmentOpenRegex, $routePatternWithoutClosingOptionals);

        if ($optionalSegmentCount !== count($segments) - 1) {
            // If there are any "]" in the middle of the route, throw a more specific error message
            if (preg_match($this->optionalSegmentCloseRegex, $routePatternWithoutClosingOptionals)) {
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
        $regex = preg_replace_callback(
            $this->variableSegmentRegex,
            function ($matches) use (&$variableNames, &$index) {
                $variableName = $matches['name'] ?: (string) $index;
                $variableNames[] = $variableName;
                ++$index;

                // @TODO: parse user groups in $matches[2] regex
                if ($matches['regex'] === '') {
                    $variableRegex = $this->defaultDispatchRegex;
                } else {
                    $variableRegex = $matches['regex'];
                    $variableRegexWithDelimiters = Regex::wrapWithDelimiters(
                        $matches['regex'],
                        self::REGEX_DELIMITER
                    ) . 'sx';

                    if (! Regex::isValid($variableRegexWithDelimiters)) {
                        throw BadRouteException::dueToInvalidVariableRegex(
                            $variableRegexWithDelimiters,
                            $variableName,
                            Regex::getLastErrorMessage()
                        );
                    }

                    if (Regex::of($variableRegexWithDelimiters)->hasCapturingGroups()) {
                        throw BadRouteException::forCapturingGroup($variableRegexWithDelimiters, $variableName);
                    }
                }

                return "(${variableRegex})";
            },
            $routePattern
        );

        return [
            // Don't escape a delimiter in a static route
            $variableNames === [] ? $regex : Regex::escapeDelimiters($regex, self::REGEX_DELIMITER),
            $variableNames,
        ];
    }
}
