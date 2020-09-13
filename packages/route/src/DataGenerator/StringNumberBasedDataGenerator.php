<?php

namespace Rosem\Component\Route\DataGenerator;

use Rosem\Component\Route\Contract\RegexRouteInterface;

use function ceil;
use function count;
use function rtrim;
use function str_pad;
use function strlen;

class StringNumberBasedDataGenerator extends AbstractRegexBasedDataGenerator
{
    public const KEY_REGEX = 0;

    public const KEY_SUFFIX = 1;

    public const KEY_SEGMENT_COUNT = 2;

    public const KEY_LAST_CHUNK_OFFSET = 3;

    protected int $lastChunkOffset = 0;

    /**
     * NumberBasedChunk constructor.
     *
     * @param int      $routeCountPerRegex
     * @param int|null $regexMaxLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        int $routeCountPerRegex = 900,
        ?int $regexMaxLength = null
    ) {
        parent::__construct($routeCountPerRegex, $regexMaxLength);

        $routeMaxCountLength = strlen((string)($routeCountPerRegex - 1));
        $this->routeExpressions[] = [
            self::KEY_REGEX => '',
            self::KEY_SUFFIX =>
                '/' . str_pad('', 10 * $routeMaxCountLength, '0123456789') . '/',
            self::KEY_SEGMENT_COUNT => (int)ceil($routeMaxCountLength / 2),
            self::KEY_LAST_CHUNK_OFFSET => $this->lastChunkOffset,
        ];
    }

    public function newChunk(): void
    {
        $this->regexTree->clear();
        $this->lastChunkOffset = $this->lastInsertId;
        //        $this->routeCountPerRegex = 900; // TODO: auto-generate
        $routeMaxCountLength = strlen((string)($this->routeCountPerRegex - 1));
        $this->routeExpressions[] = [
            self::KEY_REGEX => '',
            self::KEY_SUFFIX =>
                '/' . str_pad('', 10 * $routeMaxCountLength, '0123456789') . '/',
            self::KEY_SEGMENT_COUNT => (int)ceil($routeMaxCountLength / 2),
            self::KEY_LAST_CHUNK_OFFSET => $this->lastChunkOffset,
        ];
    }

    /**
     * @param RegexRouteInterface $route
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addRoute(RegexRouteInterface $route): void
    {
        $this->lastInsertId = count($this->routeData);

        if ($this->lastInsertId - $this->lastChunkOffset >= $this->routeCountPerRegex) {
            $this->newChunk();
        }

        $this->addRegex($route->getRegex() . '/' . $this->convertNumberToRegex($this->lastInsertId));
        $this->routeExpressions[count($this->routeExpressions) - 1][self::KEY_REGEX] =
            '~^' . $this->regex . '.*/$~sD' . ($this->utf8 ? 'u' : '');
        $middleware = &$route->getMiddlewareExtensions();
        $this->routeData[] = [$route->getMethods(), $route->getHandler(), &$middleware, $route->getVariableNames()];
    }

    protected function convertNumberToRegex(int $number): string
    {
        $numberString = (string)$number;
        $numberLength = strlen($numberString);
        $numberParts = (int)ceil($numberLength / 2);
        $index = 0;
        $previousLeftNumber = -1;
        $previousRightNumber = 0;
        $leftPart = '';
        $rightPart = '';

        do {
            $leftNumber = (int)$numberString[$index];
            $leftPart .= (($leftNumber !== ($previousLeftNumber + 1) % 10) ? '.*(' : '(') . $leftNumber;
            $previousLeftNumber = $leftNumber;
            $rightNumber = (int)$numberString[-$index - 1];
            $rightPart = (($rightNumber + 1) % 10 !== $previousRightNumber ? ').*' : ')') . $rightPart;
            $previousRightNumber = $rightNumber;
            ++$index;

            if ($numberParts === 1) { // last iteration
                if (!($numberLength % 2)) { // even length number
                    $leftPart .= (($leftNumber + 1) % 10 !== $rightNumber ? '.*' : '') . $rightNumber;
                }

                continue;
            }

            $rightPart = $rightNumber . $rightPart;
        } while (--$numberParts);

        return rtrim($leftPart . $rightPart, '.*');
    }
}
