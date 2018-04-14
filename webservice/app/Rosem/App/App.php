<?php

namespace Rosem\App;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\App\AppInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Rosem\Container\Container;
use Zend\Diactoros\Server;

class App extends Container implements AppInterface
{
    use ConfigFileTrait;

    /**
     * @var RequestHandlerInterface
     */
    protected $nextHandler;

    /**
     * @var RequestHandlerInterface
     */
    protected $defaultHandler;

    public function __construct()
    {
        parent::__construct();

        $this->defaultHandler = $this->getDefaultHandler();
        $this->nextHandler = $this->defaultHandler;
        $this->set(AppInterface::class, function () {
            return $this;
        });
    }

    protected function addServiceProvider(ServiceProviderInterface $serviceProvider)
    {
        foreach ($serviceProvider->getFactories() as $key => $factory) {
            $this->set($key, $factory);
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
                $serviceProviders[] = $serviceProvider = new $serviceProviderClass; //TODO: exception
                $this->set($serviceProviderClass, function () use ($serviceProvider) {
                    return $serviceProvider;
                });
                $this->addServiceProvider($serviceProvider);
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
                $this->extend($key, $factory);
            }
        }
    }

    protected function getDefaultHandler()
    {
        $nextHandler = &$this->defaultHandler;

        return new class ($this, $nextHandler) implements RequestHandlerInterface
        {
            /**
             * @var ContainerInterface
             */
            private $container;

            private $nextHandler;

            public function __construct(ContainerInterface $container, &$nextHandler)
            {
                $this->container = $container;
                $this->nextHandler = &$nextHandler;
            }

            public function &getNextHandlerPointer()
            {
                return $this->nextHandler;
            }

            /**
             * Handle the request and return a response.
             *
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = $this->container->get(ResponseFactoryInterface::class)->createResponse(500);
                $response->getBody()->write('<h1>Internal server error</h1>');

                return $response;
            }
        };
    }

    protected function addMiddleware(string $middleware)
    {
        $this->nextHandler = &$this->nextHandler->getNextHandlerPointer();
        $this->nextHandler = new MiddlewareRequestHandler($this, $middleware);
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
        $request = $this->get(ServerRequestFactoryInterface::class)
            ->createServerRequestFromArray($_SERVER)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withCookieParams($_COOKIE)
            ->withUploadedFiles($_FILES);
        $this->nextHandler = &$this->nextHandler->getNextHandlerPointer();
        $this->nextHandler = $this->getDefaultHandler();
        $response = $this->defaultHandler->handle($request);
        $server = new Server(function () {
        }, $request, $response);
        $server->listen();
    }
}
