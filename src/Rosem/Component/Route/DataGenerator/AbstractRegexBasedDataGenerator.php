<?php

namespace Rosem\Component\Route\DataGenerator;

use InvalidArgumentException;
use Rosem\Component\Route\{
    Exception\TooLongRouteException,
    RegexBasedDataGeneratorInterface,
    RegexTreeNode
};

use function strlen;

abstract class AbstractRegexBasedDataGenerator implements RegexBasedDataGeneratorInterface
{
    /**
     * Collection of regexes.
     *
     * @var array
     */
    public array $routeExpressions = [];

    /**
     * Data of each route in the collection.
     *
     * @var array
     */
    public array $routeData = [];

    /**
     * Count of routes per one regex.
     *
     * @var int
     */
    protected int $routeCountPerRegex;

    /**
     * Last inserted route id.
     *
     * @var int
     */
    protected int $lastInsertId = 0;

    /**
     * The final regex.
     *
     * @var string
     */
    protected string $regex = '';

    /**
     * Max length of the final regex.
     *
     * @var int
     */
    protected int $regexMaxLength;

    /**
     * Regex tree optimizer.
     *
     * @var RegexTreeNode
     */
    protected RegexTreeNode $regexTree;

    /**
     * UTF-8 flag.
     *
     * @var bool
     */
    protected bool $utf8 = false;

    /**
     * NumberBasedChunk constructor.
     *
     * @param int      $routeCountPerRegex
     * @param int|null $regexMaxLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        int $routeCountPerRegex = PHP_INT_MAX,
        ?int $regexMaxLength = null
    ) {
        if ($routeCountPerRegex <= 0) {
            throw new InvalidArgumentException('Limit of routes should be a positive integer number');
        }

        $this->routeCountPerRegex = $routeCountPerRegex;
        $this->regexMaxLength = $regexMaxLength ?: (int)ini_get('pcre.backtrack_limit') ?: 1_000_000;
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

    /**
     * Create a new regex tree to avoid data sharing.
     */
    public function __clone()
    {
        $this->regexTree = new RegexTreeNode();
    }
}
