<?php

namespace Rosem\App;

use Closure;
use Exception;
use GraphQL\GraphQL;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\Env\EnvInterface;
use Rosem\Container\Exception\ContainerException;
use Rosem\Container\ReflectionContainer;
use Rosem\Env\Env;
use Rosem\EventManager\EventManager;
use Psrnext\App\{
    AppConfigInterface, AppInterface
};
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\EventManager\EventInterface;
use Psrnext\Http\Factory\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Zend\Diactoros\Server;

class App extends ReflectionContainer implements AppInterface
{
    use FileConfigTrait;

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

    protected function addServiceProvider(ServiceProviderInterface $serviceProvider)
    {
        foreach ($serviceProvider->getFactories() as $key => $factory) {
            if (\is_array($factory)) {
                $app = $this;
                $serviceProvider = reset($factory);
                $method = next($factory);
                $this->share(
                    $key,
                    function () use ($app, $serviceProvider, $method) {
                        return $app->make($serviceProvider)->$method($app);
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function loadServiceProviders(string $serviceProvidersConfigFilePath)
    {
        /** @var ServiceProviderInterface[] $serviceProviders */
        $serviceProviders = [];

        // 1. In the first pass, the container calls the getFactories method of all service providers.
        foreach (self::getConfiguration($serviceProvidersConfigFilePath) as $serviceProviderClass) {
            if (
                \is_string($serviceProviderClass) &&
                class_exists($serviceProviderClass)
            ) {
                $this->addServiceProvider($serviceProviders[] = $this->defineNow($serviceProviderClass)->make());
            } else {
                throw new Exception(
                    'An item of service providers configuration should be a string ' .
                    'that represents service provider class which implements ' .
                    ServiceProviderInterface::class . ", got $serviceProviderClass");
            }
        }

        // 2. In the second pass, the container calls the getExtensions method of all service providers.
        foreach ($serviceProviders as $serviceProvider) {
            foreach ($serviceProvider->getExtensions() as $key => $factory) {
                $this->find($key)->withFunctionCall(
                    is_array($factory) ? Closure::fromCallable($factory) : $factory
                )->commit();
            }
        }
    }

    /**
     * @return RequestHandlerInterface
     */
    protected function getDefaultHandler()
    {
        return new class ($this) implements RequestHandlerInterface
        {
            private $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            /**
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             * @throws \Psr\Container\ContainerExceptionInterface
             * @throws \Psr\Container\NotFoundExceptionInterface
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = $this->container->get(ResponseFactoryInterface::class)->createResponse(500);
                $response->getBody()->write('Internal server error');

                return $response;
            }
        };
    }

    protected function addMiddleware(string $middleware)
    {
        $this->nextHandler = new MiddlewareRequestHandler($this, $middleware, $this->nextHandler);
    }

    /**
     * @param string $middlewaresConfigFilePath
     *
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function loadMiddlewares(string $middlewaresConfigFilePath)
    {
        $this->nextHandler = $this->getDefaultHandler();

        foreach (self::getConfiguration($middlewaresConfigFilePath) as $middlewareClass) {
            if (
                \is_string($middlewareClass) &&
                class_exists($middlewareClass)
            ) {
                $this->addMiddleware($middlewareClass);
            } else {
                throw new Exception(
                    'An item of middlewares configuration should be a string ' .
                    'that represents middleware class which implements ' .
                    MiddlewareInterface::class . ", got $middlewareClass");
            }
        }
    }

    /**
     * @param string $appConfigFilePath
     *
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function boot(string $appConfigFilePath)
    {
        $env = new Env(\dirname(getcwd()));
        $env->load();
        $this->instance(EnvInterface::class, $env)->commit();
        $this->instance(
            AppConfigInterface::class,
            new AppConfig(self::getConfiguration($appConfigFilePath))
        )->commit();
        $request = $this->get(ServerRequestFactoryInterface::class)
            ->createServerRequestFromArray($_SERVER)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withCookieParams($_COOKIE)
            ->withUploadedFiles($_FILES);
        $response = $this->nextHandler->handle($request);
        $server = new Server(function () {
        }, $request, $response);

        $this->testGraph($request, $response);

        $server->listen();
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

    public function testGraph(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        $graph = $this->get(\Psrnext\GraphQL\GraphInterface::class);

        try {
            $input = $request->getQueryParams(); // GET

            if (!isset($input['query'])) { // POST
                $input = json_decode($request->getBody()->getContents(), true);
            }

            $result = GraphQL::executeQuery(
                $graph->schema()->create(),
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

//        $response = new \Zend\Diactoros\Response\JsonResponse(json_encode($output));
        $response->getBody()->write(json_encode($output));
//        header('Content-Type: application/json; charset=UTF-8');
//        echo json_encode($output);
    }
}
