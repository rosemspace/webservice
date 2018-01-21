<?php

namespace Rosem\Kernel;

use Closure;
use Dotenv\Dotenv;
use Exception;
use GraphQL\GraphQL;
use TrueStd\Container\ServiceProviderInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Rosem\Access\Database\Models\{
    User, UserRole
};
use TrueCode\Container\Container;
use TrueCode\EventManager\EventManager;
use TrueStd\Application\AppInterface;
use TrueStd\EventManager\EventInterface;
use Zend\Diactoros\Server;

class App extends Container implements AppInterface
{
    use ConfigTrait;

    public function __construct()
    {
        parent::__construct();

        $this->instance(ContainerInterface::class, $this)->commit();
        $this->alias(ContainerInterface::class, AppInterface::class);
    }

    /**
     * @param string $serviceProvidersConfigFilePath
     *
     * @throws Exception
     */
    public function addServiceProviders(string $serviceProvidersConfigFilePath)
    {
        /** @var ServiceProviderInterface[] $serviceProviderInstances */
        $serviceProviderInstances = [];

        // 1. In the first pass, the container calls the getFactories method of all service providers.
        foreach (self::getConfiguration($serviceProvidersConfigFilePath) as $serviceProviderClass) {
            if (
                is_string($serviceProviderClass) &&
                class_exists($serviceProviderClass) &&
                $serviceProviderClass !== static::class
            ) {
                $serviceProvider = new $serviceProviderClass;

                if ($serviceProvider instanceof ServiceProviderInterface) {
                    $serviceProviderInstances[] = $serviceProvider;

                    foreach ($serviceProvider->getFactories() as $key => $factory) {
                        if (is_array($factory)) {
                            $app = $this;
                            $serviceProvider = reset($factory);
                            $method = next($factory);
                            $this->share(
                                $key,
                                function () use ($app, $serviceProvider, $method) {
                                    return (new $serviceProvider)->$method($app);
                                }
                            )->commit();
                        } else {
                            $this->share($key, $factory)->commit();
                        }
                    }
                } else {
                    throw new Exception(
                        "The service provider $serviceProviderClass should implement " .
                        ServiceProviderInterface::class
                    );
                }
            } else {
                throw new Exception(
                    'An item of service providers configuration should be a string' .
                    'that represents service provider class which implements ' .
                    ServiceProviderInterface::class . ", got $serviceProviderClass");
            }
        }

        // 2. In the second pass, the container calls the getExtensions method of all service providers.
        foreach ($serviceProviderInstances as $serviceProvider) {
            foreach ($serviceProvider->getExtensions() as $key => $factory) {
                $this->find($key)->withFunctionCall(
                    is_array($factory) ? Closure::fromCallable($factory) : $factory
                )->commit();
            }
        }
    }

    public function addMiddleware() : AppInterface
    {
        return $this;
    }

    /**
     * @param string $appConfigFilePath
     *
     * @throws Exception
     */
    public function boot(string $appConfigFilePath)
    {
        (new Dotenv(realpath(getcwd() . '/..')))->load();

        foreach (self::getConfiguration($appConfigFilePath) as $key => $data) {
            $this->instance($key, $data)->commit();
        }

        return $this->start();
    }







    public function start()
    {
        try {
            $this->testGraph();
            $this->make(Server::class, [
                function () {
                },
            ])->listen();
        } catch (ContainerExceptionInterface $e) {
            echo $e->getMessage();
        }
    }

    public function testListeners()
    {
        $em = new EventManager();
        $em->attach('user.login', function (EventInterface $event) {
            var_dump($event);
        });
        $em->attach('user.login', function (EventInterface $event) {
            var_dump($event->getTarget());
        });
        $em->attach('user.login', function (EventInterface $event) {
            var_dump($event->getTarget());
        });

        $em->trigger('user.login', $em, ['test']);
    }

    public function testGraph()
    {
        $graph = $this->get(\TrueStandards\GraphQL\GraphInterface::class);
        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->get(\Psr\Http\Message\ServerRequestInterface::class);


        try {
            $input = $request->getQueryParams(); // GET

            if (! isset($input['query'])) { // POST
                $input = json_decode($request->getBody()->getContents(), true);
            }

            $result = GraphQL::executeQuery(
                $graph->getSchema(),
                $input['query'] ?? null,
                null,
                $this,
                $input['variables'] ?? null,
                $input['operationName'] ?? null
            );
            $output = $result->toArray();
        } catch (Exception $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        $response = $this->get(\Psr\Http\Message\ResponseInterface::class);
//        $response = new \Zend\Diactoros\Response\JsonResponse(json_encode($output));
        $response->getBody()->write(json_encode($output));
//        header('Content-Type: application/json; charset=UTF-8');
//        echo json_encode($output);
    }

    public function testDB()
    {
        /** @var \Analogue\ORM\System\Mapper $roleMapper */
        /** @var \Analogue\ORM\System\Mapper $userMapper */
        $db = $this->get(\Analogue\ORM\Analogue::class);

        $roleMapper = $db->mapper(UserRole::class);

//        $adminRole = new UserRole('admin');
//        $userRole = new UserRole('user');
//        $roleMapper->store([$adminRole, $userRole]);

        $adminRole = $roleMapper->where('name', 'admin')->first();
        $userMapper = $db->mapper(User::class);
//        $roman = new User('Roman', 'Shevchenko', 'roshe@smile.fr', '123456', $adminRole);
//        $userMapper->store($roman);

        echo $userMapper->where('first_name', 'Roman')->first()->role->name;

        $response = $this->get(\Psr\Http\Message\ResponseInterface::class);
        /**
         * @var \Psr\Http\Message\ResponseInterface $response
         */
        $response->getBody()->write('<h1>' .
            $roleMapper->where('name', 'admin')->first()->name .
            '</h1>');
    }
}
