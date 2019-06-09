<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

const FIRST = 'first';
const MIDDLE = 'middle';
const LAST = 'last';
const UNKNOWN = 'unknown';
const ROSEM_ROUTER = 'rosem-route';
const FAST_ROUTER = 'fast-route';
const SYMFONY_ROUTER = 'symfony-route';

// BENCHMARK 1

$stats = [
    ROSEM_ROUTER => [], FAST_ROUTER => [], SYMFONY_ROUTER => [],
];
$options = [
    'dataGenerator' => \FastRoute\DataGenerator\GroupCountBased::class,
    'dispatcher' => \FastRoute\Dispatcher\GroupCountBased::class,
];
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

// ROSEM ROUTER ========================================================================================================
$router = new \Rosem\Component\Route\Router();
for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
    $router->addRoute('GET', '/' . $str . '/{arg}', 'handler' . $i);
    $lastStr = $str;
}
// first route ---------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/a/foo');
}
$stats[ROSEM_ROUTER][FIRST] = microtime(true) - $startTime;
if ($res[1] !== 'handler0') {
    throw new \Exception('Invalid handler');
}
// middle route --------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/es/foo');
}
$stats[ROSEM_ROUTER][MIDDLE] = microtime(true) - $startTime;
if ($res[1] !== 'handler148') {
    throw new \Exception('Invalid handler');
}
// last route ----------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/' . $lastStr . '/foo');
}
$stats[ROSEM_ROUTER][LAST] = microtime(true) - $startTime;
if ($res[1] !== 'handler' . ($nRoutes - 1)) {
    throw new \Exception('Invalid handler');
}
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    try {
        $res = $router->dispatch('GET', '/foobar/bar');
        throw new \Exception('404');
    } catch (\Exception $exception) {}
}
$stats[ROSEM_ROUTER][UNKNOWN] = microtime(true) - $startTime;
if ($res[0] !== 404) {
    throw new \Exception('Invalid response');
}
// ---------------------------------------------------------------------------------------------------------------------

// FAST ROUTER =========================================================================================================
$router = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $router) use ($nRoutes, &$lastStr) {
    for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
        $router->addRoute('GET', '/' . $str . '/{arg}', 'handler' . $i);
        $lastStr = $str;
    }
}, $options);
// first route ---------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/a/foo');
}
$stats[FAST_ROUTER][FIRST] = microtime(true) - $startTime;
// middle route --------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/es/foo');
}
$stats[FAST_ROUTER][MIDDLE] = microtime(true) - $startTime;
// last route ----------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/' . $lastStr . '/foo');
}
$stats[FAST_ROUTER][LAST] = microtime(true) - $startTime;
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/foobar/bar');
}
$stats[FAST_ROUTER][UNKNOWN] = microtime(true) - $startTime;
// ---------------------------------------------------------------------------------------------------------------------

// SYMFONY ROUTER ======================================================================================================
$routes = new \Symfony\Component\Routing\RouteCollection();
for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
    $routes->add('handler' . $i, new \Symfony\Component\Routing\Route('/' . $str . '/{arg}'));
    $lastStr = $str;
}
$dumper = new \Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper($routes);
$dump = $dumper->dump();
eval('?'.'>'.$dump);
$router = new \ProjectUrlMatcher(new \Symfony\Component\Routing\RequestContext());
// first route ---------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/a/foo');
}
$stats[SYMFONY_ROUTER][FIRST] = microtime(true) - $startTime;
// middle route --------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/es/foo');
}
$stats[SYMFONY_ROUTER][MIDDLE] = microtime(true) - $startTime;
// last route ----------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/' . $lastStr . '/foo');
}
$stats[SYMFONY_ROUTER][LAST] = microtime(true) - $startTime;
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    try {
        $router->match('/foobar/bar');
    } catch (\Exception $exception) {}
}
$stats[SYMFONY_ROUTER][UNKNOWN] = microtime(true) - $startTime;
// ---------------------------------------------------------------------------------------------------------------------

!d($stats);
