<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/../../../../../vendor/autoload.php';

const FIRST = 'first';
const MIDDLE = 'middle';
const LAST = 'last';
const UNKNOWN = 'unknown';
const FAST_ROUTER = 'nikic/fast-route';
const SYMFONY_ROUTER = 'symfony/routing';
const ROSEM_ROUTER = 'rosem/route-dispatcher';

// BENCHMARK 2

$stats = [FAST_ROUTER => [], SYMFONY_ROUTER => [], ROSEM_ROUTER => []];
$lastStr = null;
$nRoutes = 300;
$nArgs = 9;
$nMatches = 30000;
$args = implode('/', array_map(function($i) { return "{arg$i}"; }, range(1, $nArgs)));

echo <<<HTML
<h1>Benchmark 2:</h1>
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
$args = implode('/', array_map(function($i) { return "{arg$i}"; }, range(1, $nArgs)));
$router = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $router) use($nRoutes, $args, &$lastStr) {
    for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
        $router->addRoute('GET', '/' . $str . '/' . $args, 'handler' . $i);
        $lastStr = $str;
    }
}, [
    'dataGenerator' => \FastRoute\DataGenerator\MarkBased::class,
    'dispatcher' => \FastRoute\Dispatcher\MarkBased::class,
]);
// first route ---------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/a/' . $args);
}
$stats[FAST_ROUTER][FIRST] = microtime(true) - $startTime;
// middle route --------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/es/' . $args);
}
$stats[FAST_ROUTER][MIDDLE] = microtime(true) - $startTime;
// last route ----------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/' . $lastStr . '/' . $args);
}
$stats[FAST_ROUTER][LAST] = microtime(true) - $startTime;
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/foobar/' . $args);
}
$stats[FAST_ROUTER][UNKNOWN] = microtime(true) - $startTime;
//----------------------------------------------------------------------------------------------------------------------

// SYMFONY ROUTER ======================================================================================================
$router = new \Symfony\Component\Routing\RouteCollection();
for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
    $router->add('handler' . $i, new \Symfony\Component\Routing\Route('/' . $str . '/' . $args));
    $lastStr = $str;
}
$dumper = new \Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper($router);
//$dump = $dumper->dump();
//eval('?'.'>'.$dump);
$router = new \Symfony\Component\Routing\Matcher\CompiledUrlMatcher($dumper->getCompiledRoutes(), new \Symfony\Component\Routing\RequestContext());
// first route ---------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/a/' . $args);
}
$stats[SYMFONY_ROUTER][FIRST] = microtime(true) - $startTime;
// middle route --------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/es/' . $args);
}
$stats[SYMFONY_ROUTER][MIDDLE] = microtime(true) - $startTime;
// last route ----------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->match('/' . $lastStr . '/' . $args);
}
$stats[SYMFONY_ROUTER][LAST] = microtime(true) - $startTime;
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    try {
        $res = $router->match('/foobar/' . $args);
    } catch (\Exception $exception) {}
}
$stats[SYMFONY_ROUTER][UNKNOWN] = microtime(true) - $startTime;
// ---------------------------------------------------------------------------------------------------------------------

// ROSEM ROUTER ========================================================================================================
$router = new \Rosem\Component\Route\Router();
for ($i = 0, $str = 'a'; $i < $nRoutes; $i++, $str++) {
    $router->addRoute('GET', '/' . $str . '/' . $args, 'handler' . $i);
    $lastStr = $str;
}
// first route ---------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/a/' . $args);
}
$stats[ROSEM_ROUTER][FIRST] = microtime(true) - $startTime;
if ($res[1] !== 'handler0') {
    throw new \Exception('Invalid handler');
}
// middle route --------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/es/' . $args);
}
$stats[ROSEM_ROUTER][MIDDLE] = microtime(true) - $startTime;
if ($res[1] !== 'handler148') {
    throw new \Exception('Invalid handler');
}
// last route ----------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    $res = $router->dispatch('GET', '/' . $lastStr . '/' . $args);
}
$stats[ROSEM_ROUTER][LAST] = microtime(true) - $startTime;
if ($res[1] !== 'handler' . ($nRoutes - 1)) {
    throw new \Exception('Invalid handler');
}
// unknown route -------------------------------------------------------------------------------------------------------
$startTime = microtime(true);
for ($i = 0; $i < $nMatches; $i++) {
    try {
        $res = $router->dispatch('GET', '/foobar/' . $args);
        throw new \Exception('404');
    } catch (\Exception $exception) {}
}
$stats[ROSEM_ROUTER][UNKNOWN] = microtime(true) - $startTime;
if ($res[0] !== 404) {
    throw new \Exception('Invalid response');
}
//----------------------------------------------------------------------------------------------------------------------

!d($stats);
