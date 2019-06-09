<?php

namespace Symfony\Component\Routing;

require '../vendor/autoload.php';

$routes = new RouteCollection();

for ($i = 0; $i < 400; ++$i) {
    $routes->add('r'.$i, new Route('/abc'.$i));
    $routes->add('f'.$i, new Route('/abc{foo}/'.$i));
}

$dumper = new Matcher\Dumper\PhpMatcherDumper($routes);

eval('?'.'>'.$dumper->dump());

$router = new \ProjectUrlMatcher(new RequestContext());

$i = 10000;
$s = microtime(1);

while (--$i) {
    $res = $router->match('/abcdef/399');
}

var_dump($res);

echo 'Symfony: ', 1000 * (microtime(1) - $s), "\n";

namespace FastRoute;

$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    for ($i = 0; $i < 400; ++$i) {
        $r->addRoute('GET', '/abc'.$i, 'r'.$i);
        $r->addRoute('GET', '/abc{foo}/'.$i, 'f'.$i);
    }
});

$i = 10000;
$s = microtime(1);

while (--$i) {
    $dispatcher->dispatch('GET', '/abcdef/399');
}

echo 'FastRoute: ', 1000 * (microtime(1) - $s), "\n";
