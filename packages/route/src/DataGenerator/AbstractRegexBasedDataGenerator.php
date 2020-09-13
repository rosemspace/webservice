<?php

declare(strict_types=1);

namespace Rosem\Component\Route\DataGenerator;

use InvalidArgumentException;
use Rosem\Component\Route\Contract\RegexBasedDataGeneratorInterface;
use Rosem\Component\Route\Exception\TooLongRouteException;
use Rosem\Component\Route\RegexNode;

use function ini_get;
use function strlen;

abstract class AbstractRegexBasedDataGenerator implements RegexBasedDataGeneratorInterface
{
    /**
     * Collection of regexes.
     */
    public array $routeExpressions = [];

    /**
     * Data of each route in the collection.
     */
    public array $routeData = [];

    /**
     * Count of routes per one regex.
     */
    protected int $routeCountPerRegex;

    /**
     * Last inserted route id.
     */
    protected int $lastInsertId = 0;

    /**
     * The final regex.
     */
    protected string $regex = '';

    /**
     * Max length of the final regex.
     */
    protected int $regexMaxLength;

    /**
     * Regex tree optimizer.
     */
    protected RegexNode $regexTree;

    /**
     * UTF-8 flag.
     */
    protected bool $utf8 = false;

    /**
     * NumberBasedChunk constructor.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $routeCountPerRegex = PHP_INT_MAX, ?int $regexMaxLength = null)
    {
        if ($routeCountPerRegex <= 0) {
            throw new InvalidArgumentException('Limit of routes should be a positive integer number');
        }

        $this->routeCountPerRegex = $routeCountPerRegex;
        $this->regexMaxLength = $regexMaxLength ?: (int) ini_get('pcre.backtrack_limit') ?: 1_000_000;
        $this->regexTree = new RegexNode();
    }

    /**
     * Create a new regex tree to avoid data sharing.
     */
    public function __clone()
    {
        $this->regexTree = new RegexNode();
    }

    /**
     * Use UTF-8 flag or not.
     */
    public function useUtf8(bool $use = true): void
    {
        $this->utf8 = $use;
    }

    /**
     * Add regex to the collection.
     *
     * @throws TooLongRouteException
     */
    protected function addRegex(string $regex): void
    {
        $this->regexTree->addRegex($regex);
        $this->regex = $this->regexTree->getRegex();

        if (strlen($this->regex) > $this->regexMaxLength) {
            // TODO: rollback for regexTree

            throw new TooLongRouteException('Your route is too long');
        }
    }
}
