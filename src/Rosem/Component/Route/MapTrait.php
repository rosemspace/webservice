<?php

namespace Rosem\Component\Route;

trait MapTrait
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
    protected $staticRouteMap = [];

    /**
     * @var RegexBasedDataGeneratorInterface[]
     */
    protected $variableRouteMap = [];
}
