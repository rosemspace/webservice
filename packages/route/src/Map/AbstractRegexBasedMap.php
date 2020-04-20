<?php

namespace Rosem\Component\Route\Map;

use InvalidArgumentException;
use Rosem\Component\Route\{
    Contract\RouteParserInterface,
    Exception\TooLongRouteException,
    RouteParser,
    RegexNode
};

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
     * @inheritDoc
     */
    protected function addVariableRoute(string $scope, array $parsedRoute, $data): void
    {
        $this->variableRouteCount = count($this->variableRouteMap);

        if (!$this->variableRouteCount ||
            $this->variableRouteCount - $this->variableRouteOffset >= $this->variableRouteCountPerChunk
        ) {
            $this->createNewVariableRouteChunk($scope);
            $this->variableRouteOffset = $this->variableRouteCount;
        }

        try {
            $this->addSingleVariableRoute($scope, $parsedRoute, $data);
        } catch (TooLongRouteException $exception) {
            // TODO: add rollback
            //$this->variableRouteMap[$method]->rollback();
            $this->createNewVariableRouteChunk($scope);
            $this->addSingleVariableRoute($scope, $parsedRoute, $data);
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

    /**
     * Create a new regex tree to avoid data sharing.
     */
    public function __clone()
    {
        $this->variableRouteRegexTree = new RegexNode();
    }
}
