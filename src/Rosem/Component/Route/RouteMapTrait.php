<?php

namespace Rosem\Component\Route;

trait RouteMapTrait
{
    /**
     * [
     *     'host' => [
     *          'scheme' => [
     *              'method' => [
     *                  'path' => ['data'],
     *              ],
     *          ],
     *      ],
     * ]
     *
     * @var array[]
     */
    protected array $staticRouteMap = [];

    /**
     * @var RegexBasedDataGeneratorInterface
     */
    protected RegexBasedDataGeneratorInterface $variableRouteMap;
}
