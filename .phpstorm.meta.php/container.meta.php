<?php

namespace PHPSTORM_META
{

// this is saner and self-documented format for 2016.2 and NEWER
//Try QuickDoc on these "magic" functions, or even Go to definition!

    override(
        \Psr\Container\ContainerInterface::get(0), // method signature // argument number is ALWAYS 0 now.
        map( [ //map of argument value -> return type
            //non mapped value, e.g. $getByClassNameConst case above will be returned automatically
        ])
    );

//basicaly the same as get, just for array["arg"] lookups
    override(
        new \Psr\Container\ContainerInterface,
        map([
//            "special" => \Exception::class,
        ])
    );

//pattern example. `@` is replaced by argument literal value. //TODO: more advanced patterns.
    override(\TrueStandards\DI\ContainerInterface::getByPattern(0),
        map([
            '' => '@Iterator|\Iterator',
        ])
    );
}
