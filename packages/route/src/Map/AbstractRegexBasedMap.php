<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Map;

use InvalidArgumentException;
use Rosem\Component\Route\{
    Contract\RouteParserInterface,
    Exception\TooLongRouteException,
    RegexNode
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
     * The final regex.
     *
     * @var string
     */
    protected string $variableRouteRegex = '';

    /**
     * Max length of the final regex.
     *
     * @var int
     */
    protected int $variableRouteRegexMaxLength;

    /**
     * Regex tree optimizer.
     *
     * @var RegexNode
     */
    protected RegexNode $variableRouteRegexTree;

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
     * @param string $scope
     * @param array  $parsedRoute
     * @param mixed  $resource
     */
    abstract protected function saveVariableRoute(string $scope, array $parsedRoute, $resource): void;

    /**
     * @inheritDoc
     */
    protected function addVariableRoute(string $scope, array $parsedRoute, $resource): void
    {
        $this->variableRouteCount = count($this->variableRouteMap);

        if (!isset($this->variableRouteMapExpressions[$scope]) ||
            $this->variableRouteCount - $this->variableRouteOffset >= $this->variableRouteCountPerChunk
        ) {
            $this->createVariableRouteChunk($scope);
            $this->variableRouteOffset = $this->variableRouteCount;
        }

        try {
            $this->saveVariableRoute($scope, $parsedRoute, $resource);
        } catch (TooLongRouteException $exception) {
            // TODO: add rollback
            //$this->variableRouteMap[$method]->rollback();
            $this->createVariableRouteChunk($scope);
            $this->saveVariableRoute($scope, $parsedRoute, $resource);
        }
    }

    /**
     * Add regex to the collection.
     *
     * @param string $routePattern
     * @param string $routeRegex
     */
    protected function addVariableRouteRegex(string $routePattern, string $routeRegex): void
    {
        $this->variableRouteRegexTree->addRegex($routeRegex);
        $this->variableRouteRegex = $this->variableRouteRegexTree->getRegex();

        if (strlen($this->variableRouteRegex) > $this->variableRouteRegexMaxLength) {
            // TODO: rollback
            //$this->regexTree->rollback();

            throw new TooLongRouteException("Your route \"$routePattern\" is too long");
        }
    }
}
