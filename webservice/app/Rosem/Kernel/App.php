<?php

namespace Rosem\Kernel;

use Closure;
use Dotenv\Dotenv;
use Exception;
use GraphQL\GraphQL;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
use TrueStd\Http\Factory\ResponseFactoryInterface;
use TrueStd\Http\Factory\ServerRequestFactoryInterface;
use TrueStd\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Zend\Diactoros\Server;

class App extends Container implements AppInterface
{
    use FileConfigTrait;

    const MODE_DEVELOPMENT = 0;
    const MODE_MAINTENANCE = 1;
    const MODE_PRODUCTION  = 2;
    const MODE_TESTING     = 3;

    /**
     * @var ServiceProviderInterface[]
     */
    protected $serviceProviders = [];

    /**
     * @var RequestHandlerInterface
     */
    protected $nextHandler;

    public function __construct()
    {
        parent::__construct();

        $this->instance(ContainerInterface::class, $this)->commit();
        $this->alias(ContainerInterface::class, AppInterface::class);
    }

    public function addServiceProvider(ServiceProviderInterface $serviceProvider)
    {
        $this->serviceProviders[] = $serviceProvider;

        // 1. In the first pass, the container calls the getFactories method of all service providers.
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
    }

    /**
     * @param string $serviceProvidersConfigFilePath
     *
     * @throws Exception
     */
    public function addServiceProvidersFromFile(string $serviceProvidersConfigFilePath)
    {
        // 1. In the first pass, the container calls the getFactories method of all service providers.
        foreach (self::getConfiguration($serviceProvidersConfigFilePath) as $serviceProviderClass) {
            if (
                is_string($serviceProviderClass) &&
                class_exists($serviceProviderClass) &&
                $serviceProviderClass !== static::class &&
                ($serviceProvider = new $serviceProviderClass) instanceof ServiceProviderInterface
            ) {
                $this->addServiceProvider($serviceProvider);
            } else {
                throw new Exception(
                    'An item of service providers configuration should be a string' .
                    'that represents service provider class which implements ' .
                    ServiceProviderInterface::class . ", got $serviceProviderClass");
            }
        }

        // 2. In the second pass, the container calls the getExtensions method of all service providers.
        foreach ($this->serviceProviders as $serviceProvider) {
            foreach ($serviceProvider->getExtensions() as $key => $factory) {
                $this->find($key)->withFunctionCall(
                    is_array($factory) ? Closure::fromCallable($factory) : $factory
                )->commit();
            }
        }
    }

    protected function initDefaultHandler()
    {
        if (! $this->nextHandler) {
            $this->nextHandler = new class ($this) implements RequestHandlerInterface
            {
                private $container;

                public function __construct(ContainerInterface $container)
                {
                    $this->container = $container;
                }

                public function handle(ServerRequestInterface $request) : ResponseInterface
                {
                    $response = $this->container->get(ResponseFactoryInterface::class)->createResponse(500);
                    $response->getBody()->write('Internal server error');

                    return $response;
                }
            };
        }
    }

    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->initDefaultHandler();
        $this->nextHandler = new MiddlewareRequestHandler($middleware, $this->nextHandler);
    }

    public function addMiddlewareLayersFromFile(string $serviceProvidersConfigFilePath)
    {
        // TODO: Implement addMiddlewareLayers() method.
    }

    public function loadConfig(string $appConfigFilePath)
    {
        //TODO: move into boot method when middleware will be lazy loading
        (new Dotenv(realpath(getcwd() . '/..')))->load();

        foreach (self::getConfiguration($appConfigFilePath) as $key => $data) {
            $this->instance($key, $data)->commit();
        }
    }

    /**
     * @param string $appConfigFilePath
     *
     * @throws Exception
     */
    public function boot(string $appConfigFilePath)
    {
        $this->initDefaultHandler();
        $request = $this->get(ServerRequestFactoryInterface::class)
            ->createServerRequestFromArray($_SERVER)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withCookieParams($_COOKIE)
            ->withUploadedFiles($_FILES);
        $response = $this->nextHandler->handle($request);
        $server = new Server(function () {}, $request, $response);
        $server->listen();

//        return $this->start();
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
