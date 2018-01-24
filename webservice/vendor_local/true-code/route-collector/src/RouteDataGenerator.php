<?php

namespace TrueCode\RouteCollector;

use FastRoute\DataGenerator;
use FastRoute\DataGenerator\{
    CharCountBased, GroupCountBased, GroupPosBased, MarkBased
};

class RouteDataGenerator implements DataGenerator
{
    /**
     * Char count based type.
     */
    const DRIVER_CHAR_COUNT = 0;

    /**
     * Group count based type.
     */
    const DRIVER_GROUP_COUNT = 1;

    /**
     * Group pos based type.
     */
    const DRIVER_GROUP_POS = 2;

    /**
     * Mark based type.
     */
    const DRIVER_MARK = 3;

    /**
     * @var DataGenerator
     */
    protected $driver;

    public function __construct(int $driverType = self::DRIVER_GROUP_COUNT)
    {
        switch ($driverType) {
            case self::DRIVER_CHAR_COUNT:
                $dataGenerator = CharCountBased::class;
                break;
            case self::DRIVER_GROUP_POS:
                $dataGenerator = GroupPosBased::class;
                break;
            case self::DRIVER_MARK:
                $dataGenerator = MarkBased::class;
                break;
            case self::DRIVER_GROUP_COUNT:
            default:
                $dataGenerator = GroupCountBased::class;
        }

        $this->driver = new $dataGenerator;
    }

    /**
     * Adds a route to the data generator. The route data uses the
     * same format that is returned by RouterParser::parser().
     * The handler doesn't necessarily need to be a callable, it
     * can be arbitrary data that will be returned when the route
     * matches.
     *
     * @param string $httpMethod
     * @param array  $routeData
     * @param mixed  $handler
     */
    public function addRoute($httpMethod, $routeData, $handler)
    {
        $this->driver->addRoute($httpMethod, $routeData, $handler);
    }

    /**
     * Returns dispatcher data in some unspecified format, which
     * depends on the used method of dispatch.
     */
    public function getData()
    {
        return $this->driver->getData();
    }
}
