<?php

namespace Bench;

require __DIR__ . '/../../../../../vendor/autoload.php';

const NUMBER_OF_ROUTES = 1000;

namespace Symfony\Component\Routing;

$routes = new RouteCollection();

for ($i = 0; $i < 400; ++$i) {
    $routes->add('r'.$i, new Route('/abc'.$i));
    $routes->add('f'.$i, new Route('/abc{foo}/'.$i));
}

$dumper = new Matcher\Dumper\CompiledUrlMatcherDumper($routes);

//eval('?'.'>'.$dumper->dump());

$router = new \Symfony\Component\Routing\Matcher\CompiledUrlMatcher($dumper->getCompiledRoutes(), new RequestContext());

$i = \Bench\NUMBER_OF_ROUTES;
$s = microtime(1);

while (--$i) {
    $res = $router->match('/abcdef/399');
}

echo 'Symfony: ', 1000 * (microtime(1) - $s), "\n";

namespace FastRoute;

$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    for ($i = 0; $i < 400; ++$i) {
        $r->addRoute('GET', '/abc'.$i, 'r'.$i);
        $r->addRoute('GET', '/abc{foo}/'.$i, 'f'.$i);
    }
});

$i = \Bench\NUMBER_OF_ROUTES;
$s = microtime(1);

while (--$i) {
    $res = $dispatcher->dispatch('GET', '/abcdef/399');
}

echo 'FastRoute: ', 1000 * (microtime(1) - $s), "\n";
