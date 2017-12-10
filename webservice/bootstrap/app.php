<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

class Img
{
    protected $extension;

    public function __invoke($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    public function getExtension()
    {
        return ".$this->extension";
    }
}

class Brand
{
    protected $brand;
    public $logo;

    public function __construct($brand, Img $logo)
    {
        $this->brand = $brand;
        $this->logo = $logo;
    }

    public function getBrand()
    {
        return $this->brand;
    }
}

class User
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function sayHello(Brand $brand, $end)
    {
        return "Hi, my name is $this->name The brand is {$brand->getBrand()}{$brand->logo->getExtension()} $end";
    }

    public function __invoke()
    {
        return $this->name;
    }
}

function test($name) {
    return "HEllo, my name is $name";
}

$container = new True\Support\DI\Container();
$container->set('user', [User::class => 'sayHello']);

var_dump($container->make('user', ['Roman'], [['BMW', ['png']], '!!!!']));

die;

$container->singleton('test', 'test', ['Romanna']);
//var_dump($container->get('user', ['Roman']));
//var_dump($container->get('test', ['Roman']));

die;

$app = new True\Support\DI\Container(
//    realpath(__DIR__.'/../')
);

class Text
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function get($name)
    {
        return "TEXT, $name";
    }
}
$app->simuton('text', 'Text::get', ['Roman'], ['Shevchenko']);

var_dump($app->make('text'));
var_dump($app->make('text', ['Romanna']));

die;

class Inject
{
    protected $test;

    public function __construct($test)
    {
        $this->test = $test;
    }

    public function tryInject($name)
    {
        return "You $name are so good";
    }

    public function tryInject2()
    {
        return "Your container is so bad";
    }
}

class Rosem implements \True\Standards\DI\InjectionInterface
{
    /** @var Inject */
    public $inject;

    public $name;

    public function __construct(Inject $inject, $name)
    {
        $this->inject = $inject;
        $this->name = $name;
        echo $this->name;
    }

    public function sayHello($name)
    {
        return $this->inject->tryInject($name);
    }

    public function __invoke(Inject $inject)
    {
        return $inject->tryInject2();
    }

    /**
     * Executes after construct.
     */
    public function __inject()//Inject $inject)
    {
        //$this->inject = $inject;
    }
}

$app->bind('test', 'Rosem');

var_dump($app->make('test', ['INJEct', 'Romanna'], ['Invoke params']));//->sayHello('GOOD'));

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
