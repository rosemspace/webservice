<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Map;

use InvalidArgumentException;
use Rosem\Component\Route\{
    Contract\RouteInterface,
    Contract\RouteParserInterface,
    Exception\BadRouteException,
    Exception\TooLongRouteException,
    Regex,
    RegexNode,
    Route,
    RouteParser
};

use function count;
use function ini_get;
use function min;
use function strlen;

/**
 * Class AbstractRegexBasedMap.
 */
abstract class AbstractRegexBasedMap extends AbstractMap
{
    /**
     * Default count of routes per regular expression.
     */
    public const DEFAULT_ROUTE_COUNT_PER_CHUNK = PHP_INT_MAX;

    /**
     * Fallback length of a regex if it's not set in php.ini.
     */
    public const REGEX_MAX_LENGTH = 1_000_000;

    /**
     * A number of routes per one chunk.
     *
     * @var int
     */
    protected int $variableRouteCountPerChunk;

    /**
     * Max length of the final regex.
     *
     * @var int
     */
    protected int $variableRouteRegexMaxLength;

    /**
     * A number of inserted routes excluding routes in the current chunk.
     *
     * @var int
     */
    protected int $variableRouteOffset = 0;

    /**
     * A number of inserted routes.
     *
     * @var int
     */
    protected int $variableRouteCount = 0;

    /**
     * Collection of regular expressions.
     *
     * @var array
     */
    protected array $variableRouteMapExpressions = [];

    /**
     * Collection of route resources.
     *
     * @var array
     */
    protected array $variableRouteMapData = [];

    /**
     * The final regex.
     *
     * @var string
     */
    protected string $variableRouteRegex = '';

    /**
     * Regex tree optimizer.
     *
     * @var RegexNode
     */
    protected RegexNode $variableRouteRegexTree;

    /**
     * Determine variable routes validation.
     *
     * @var bool
     */
    protected bool $validateFlag = false;

    /**
     * AbstractRegexBasedMap constructor.
     *
     * @param RouteParserInterface $parser
     * @param int                  $variableRouteCountPerChunk
     * @param int|null             $variableRouteRegexMaxLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        RouteParserInterface $parser,
        int $variableRouteCountPerChunk = self::DEFAULT_ROUTE_COUNT_PER_CHUNK,
        ?int $variableRouteRegexMaxLength = null
    ) {
        if ($variableRouteCountPerChunk <= 0) {
            throw new InvalidArgumentException('Limit of routes should be a positive integer number');
        }

        if ($variableRouteRegexMaxLength === null) {
            $pcreBacktrackLimit = ini_get('pcre.backtrack_limit');
            $variableRouteRegexMaxLength = min(
                $pcreBacktrackLimit === '' ? self::REGEX_MAX_LENGTH : (int)$pcreBacktrackLimit,
                self::REGEX_MAX_LENGTH
            );
        }

        if ($variableRouteRegexMaxLength <= 0) {
            throw new InvalidArgumentException(
                'Length of a regular expression should be a positive integer number'
            );
        }

        parent::__construct($parser);

        $this->variableRouteCountPerChunk = $variableRouteCountPerChunk;
        $this->variableRouteRegexMaxLength = $variableRouteRegexMaxLength;
        $this->variableRouteRegexTree = new RegexNode();
    }

    /**
     * Create a new regex tree to avoid data sharing.
     */
    public function __clone()
    {
        $this->variableRouteRegexTree = new RegexNode();
    }

    /**
     * Prepare internal data for the new chunk.
     *
     * @param string $scope
     *
     * @return void
     */
    abstract protected function createVariableRouteChunk(string $scope): void;

    /**
     * Save variable route based on a regular expression to the collection.
     *
     * @param RouteInterface $route
     */
    abstract protected function saveVariableRoute(RouteInterface $route): void;

    /**
     * @inheritDoc
     */
    protected function addVariableRoute(string $scope, string $routePattern, $resource, array $meta): void
    {
        [$routeRegex] = $meta;

        if (isset($this->variableRouteMap[$scope][$routeRegex])) {
            throw BadRouteException::forDuplicatedRoute($routeRegex, $scope);
        }

        $this->variableRouteMap[$scope][$routeRegex] = new Route($scope, $routePattern, $resource, $meta);
    }

    /**
     * @inheritDoc
     */
    public function compile(): void
    {
        if ($this->variableRouteCount > 0) {
            return;
        }

        foreach ($this->variableRouteMap as $scope => $routes) {
            foreach ($routes as $route) {
                $this->variableRouteCount = count($this->variableRouteMapData);

                if (!isset($this->variableRouteMapExpressions[$scope]) ||
                    $this->variableRouteCount - $this->variableRouteOffset >= $this->variableRouteCountPerChunk
                ) {
                    $this->generateVariableRouteRegex($scope);
                    $this->createVariableRouteChunk($scope);
                    $this->variableRouteOffset = $this->variableRouteCount;
                }

                try {
                    $this->saveVariableRoute($route);

                    if ($this->validateFlag) {
                        $this->assertVariableRouteValid($route);
                    }
                } catch (TooLongRouteException $exception) {
                    // Try to save the route in a new chunk.
                    // Maybe it's too long to include in the current chunk but long enough to include in the new chunk
                    // TODO: add rollback
                    //$this->variableRouteMap[$method]->rollback();
                    //$this->regexTree->rollback();
                    $this->generateVariableRouteRegex($scope);
                    $this->createVariableRouteChunk($scope);
                    $this->saveVariableRoute($route);

                    if ($this->validateFlag) {
                        $this->assertVariableRouteValid($route);
                    }
                }
            }

            $this->generateVariableRouteRegex($scope);
        }

        if (!Regex::isValid($this->variableRouteRegex)) {
            $this->validateFlag = true;
            $this->variableRouteRegexTree->clear();
            $this->variableRouteMapExpressions = [];
            $this->variableRouteMapData = [];
            $this->variableRouteOffset = $this->variableRouteCount = 0;
            $this->compile();
            $lastException = Regex::getLastException();

            throw new BadRouteException($lastException->getMessage(), $lastException->getCode());
        }
    }

    private function generateVariableRouteRegex(string $scope): void
    {
        if (!isset($this->variableRouteMapExpressions[$scope])) {
            return;
        }

        $this->variableRouteRegex = Regex::wrapWithDelimiters(
                '^' . $this->variableRouteRegexTree->getRegex() . '$',
                RouteParser::REGEX_DELIMITER
            ) . 'sD';// @TODO . ($isHost ? 'i' : '');

        if ($this->utf8) {
            $this->variableRouteRegex .= 'u';
        }

        $this->variableRouteMapExpressions[$scope][count($this->variableRouteMapExpressions[$scope]) - 1] =
            $this->variableRouteRegex;
    }

    private function assertVariableRouteValid(RouteInterface $route)
    {
        $this->generateVariableRouteRegex($route->getScope());
        $routePattern = $route->getPathPattern();
        [$routeRegex] = $route->getMeta();

        if (strlen($this->variableRouteRegex) > $this->variableRouteRegexMaxLength) {
            $this->variableRouteRegexTree->clear();
            $this->variableRouteMapExpressions = [];

            throw TooLongRouteException::dueToLongRoute($routePattern);
        }

        if (!Regex::isValid($this->variableRouteRegex)) {
            throw BadRouteException::dueToIncompatibilityWithPreviousPattern($routePattern, $routeRegex);
        }
    }
}
