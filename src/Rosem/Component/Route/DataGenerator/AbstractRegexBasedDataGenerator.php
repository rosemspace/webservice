<?php

namespace Rosem\Component\Route\DataGenerator;

use InvalidArgumentException;
use Rosem\Component\Route\{
    Exception\TooLongRouteException, RegexBasedDataGeneratorInterface, RegexTreeNode
};
use function strlen;

abstract class AbstractRegexBasedDataGenerator implements RegexBasedDataGeneratorInterface
{
    /**
     * Collection of regexes.
     *
     * @var array
     */
    public $routeExpressions = [];

    /**
     * Data of each route in the collection.
     *
     * @var array
     */
    public $routeData = [];

    /**
     * Count of routes per one regex.
     *
     * @var int
     */
    protected $routeCountPerRegex;

    /**
     * Last inserted route id.
     *
     * @var int
     */
    protected $lastInsertId = 0;

    /**
     * The final regex.
     *
     * @var string
     */
    protected $regex = '';

    /**
     * Max length of the final regex.
     *
     * @var int
     */
    protected $regexMaxLength;

    /**
     * Regex tree optimizer.
     *
     * @var RegexTreeNode
     */
    protected $regexTree;

    /**
     * UTF-8 flag.
     *
     * @var bool
     */
    protected $utf8 = false;

    /**
     * NumberBasedChunk constructor.
     *
     * @param int      $routeCountPerRegex
     * @param int|null $regexMaxLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        int $routeCountPerRegex,
        ?int $regexMaxLength = null
    ) {
        if ($routeCountPerRegex <= 0) {
            throw new InvalidArgumentException('Limit of routes should be a positive integer number');
        }

        $this->routeCountPerRegex = $routeCountPerRegex;
        $this->regexMaxLength = $regexMaxLength ?: (int)ini_get('pcre.backtrack_limit') ?: 1000000;
        $this->regexTree = new RegexTreeNode();
    }

    /**
     * Use UTF-8 flag or not.
     *
     * @param bool $use
     */
    public function useUtf8(bool $use = true): void
    {
        $this->utf8 = $use;
    }

    /**
     * Add regex to the collection.
     *
     * @param string $regex
     *
     * @throws TooLongRouteException
     */
    protected function addRegex(string $regex): void
    {
        $this->regexTree->addRegex($regex);
        $this->regex = $this->regexTree->getRegex();

        if (strlen($this->regex) > $this->regexMaxLength) {
            // TODO: rollback
//            $this->regexTree->rollback();

            throw new TooLongRouteException('Your route is too long');
        }
    }
}
