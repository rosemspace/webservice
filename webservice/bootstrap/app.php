<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Rosem application instance
| which serves as the "glue" for all the components of Rosem, and is
| the IoC container for the system binding all of the various parts.
|
*/

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

//$app = new True\DI\Container;
//$app->delegate(new \True\DI\ReflectionContainer);
//
//class Test1
//{
//    protected $prop1;
//
//    public function method1($arg1)
//    {
//        $this->prop1 = $arg1;
//
//        echo "Test 1 with arg '$arg1'";
//    }
//}
//
//class Test2
//{
//    protected $test1;
//
//    public function __construct(Test1 $test1)
//    {
//        $this->test1 = $test1;
//    }
//
//    public function getTest2()
//    {
//        return 'Test 2 -> ' . $this->test1->method1();
//    }
//}
//
////$binding = (new \True\DI\Binding\ClassBinding($app, 'Test1', Test1::class))->withMethodCall('method1', ['some arg']);
////var_dump($binding->make()); die;
//
//$app->bind('Test1', Test1::class)->withMethodCall('method1', ['some arg']);
//$test1 = $app->get('Test1');
//$test11 = $app->get('Test1');
//var_dump($test1, $test11);
////echo $test1->getTest2();
//
//die;

$app = Rosem\Kernel\AppFactory::create('service_providers.php');
$app->boot('app.php');
