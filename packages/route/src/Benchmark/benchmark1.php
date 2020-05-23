<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set('scream.enabled', false);
ini_set('xdebug.scream', false);
error_reporting(E_ALL);

require __DIR__ . '/../../../../vendor/autoload.php';

const FIRST = 'first';
const MIDDLE = 'middle';
const LAST = 'last';
const UNKNOWN = 'unknown';
const FAST_ROUTER = 'nikic/fast-route';
const SYMFONY_ROUTER = 'symfony/routing';
const ROSEM_ROUTER = 'rosem/route-dispatcher';

// BENCHMARK 1

$stats = [FAST_ROUTER => [], SYMFONY_ROUTER => [], ROSEM_ROUTER => []];
$lastStr = null;
$nRoutes = 300;
$nArgs = 1;
$nMatches = 30000;

echo <<<HTML
<h1>Benchmark 1:</h1>
<table>
    <tbody>
        <tr>
            <td>Routes
            <td>$nRoutes
        <tr>
            <td>Arguments
            <td>$nArgs
        <tr>
            <td>Matches
            <td>$nMatches
    </tbody>
</table>
HTML;

// FAST ROUTER =========================================================================================================
$router = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $router) use ($nRoutes, &$lastStr) {
    for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
        $router->addRoute('GET', '/' . $str . '/{arg}', 'handler' . $i);
        $lastStr = $str;
    }
}, [
    'dataGenerator' => \FastRoute\DataGenerator\MarkBased::class,
    'dispatcher' => \FastRoute\Dispatcher\MarkBased::class,
]);
// first route ---------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/a/foo');
}
$stats[FAST_ROUTER][FIRST] = (hrtime(true) - $startTime) / 1e6;
// middle route --------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/es/foo');
}
$stats[FAST_ROUTER][MIDDLE] = (hrtime(true) - $startTime) / 1e6;
// last route ----------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/' . $lastStr . '/foo');
}
$stats[FAST_ROUTER][LAST] = (hrtime(true) - $startTime) / 1e6;
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/foobar/bar');
}
$stats[FAST_ROUTER][UNKNOWN] = (hrtime(true) - $startTime) / 1e6;
// ---------------------------------------------------------------------------------------------------------------------

// SYMFONY ROUTER ======================================================================================================
$routes = new \Symfony\Component\Routing\RouteCollection();
for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
    $routes->add('handler' . $i, new \Symfony\Component\Routing\Route('/' . $str . '/{arg}'));
    $lastStr = $str;
}
$dumper = new \Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper($routes);
//$dump = $dumper->dump();
//eval('?'.'>'.$dump);
$router = new \Symfony\Component\Routing\Matcher\CompiledUrlMatcher($dumper->getCompiledRoutes(), new \Symfony\Component\Routing\RequestContext());
// first route ---------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/a/foo');
}
$stats[SYMFONY_ROUTER][FIRST] = (hrtime(true) - $startTime) / 1e6;
// middle route --------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/es/foo');
}
$stats[SYMFONY_ROUTER][MIDDLE] = (hrtime(true) - $startTime) / 1e6;
// last route ----------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/' . $lastStr . '/foo');
}
$stats[SYMFONY_ROUTER][LAST] = (hrtime(true) - $startTime) / 1e6;
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
//    try {
        $router->match('/foobar/bar');
//    } catch (\Exception $exception) {}
}
$stats[SYMFONY_ROUTER][UNKNOWN] = (hrtime(true) - $startTime) / 1e6;
// ---------------------------------------------------------------------------------------------------------------------

// ROSEM ROUTER ========================================================================================================
$router = new \Rosem\Component\Route\Router(new \Rosem\Component\Route\RouteParser());
for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
    $router->addRoute('GET', '/' . $str . '/{arg}', 'handler' . $i);
    $lastStr = $str;
}
$router->compile();
// first route ---------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/a/foo');
}
$stats[ROSEM_ROUTER][FIRST] = (hrtime(true) - $startTime) / 1e6;
if ($res[1] !== 'handler0') {
    throw new \Exception('Invalid handler');
}
// middle route --------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/es/foo');
}
$stats[ROSEM_ROUTER][MIDDLE] = (hrtime(true) - $startTime) / 1e6;
if ($res[1] !== 'handler148') {
    throw new \Exception('Invalid handler');
}
// last route ----------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/' . $lastStr . '/foo');
}
$stats[ROSEM_ROUTER][LAST] = (hrtime(true) - $startTime) / 1e6;
if ($res[1] !== 'handler' . ($nRoutes - 1)) {
    throw new \Exception('Invalid handler');
}
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = hrtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/foobar/bar');
}
$stats[ROSEM_ROUTER][UNKNOWN] = (hrtime(true) - $startTime) / 1e6;
if (count($res) !== 1) {
    throw new \Exception('Invalid response');
}
// ---------------------------------------------------------------------------------------------------------------------
//echo $router->variableRouteMapExpressions['GET'][0];
!d($stats);
