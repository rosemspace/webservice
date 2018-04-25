<?php

namespace Rosem\App;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\App\AppInterface;
use Psrnext\Http\Factory\{
    ResponseFactoryInterface
};
use Zend\Diactoros\{
    Server, ServerRequestFactory
};

class App implements AppInterface
{
    use ConfigFileTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestHandlerInterface
     */
    protected $nextHandler;

    /**
     * @var RequestHandlerInterface
     */
    protected $defaultHandler;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->defaultHandler = $this->getDefaultHandler();
        $this->nextHandler = $this->defaultHandler;
    }

    protected function getDefaultHandler()
    {
        $nextHandler = &$this->defaultHandler;
        $container = &$this->container;

        return new class ($container, $nextHandler) implements RequestHandlerInterface
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
            public function handle(ServerRequestInterface $request) : ResponseInterface
            {
                $response = $this->container->get(ResponseFactoryInterface::class)->createResponse(500);
                $response->getBody()->write('<h1>Internal server error</h1>');

                return $response;
            }
        };
    }

    /**
     * @param string $middleware
     * @param float  $priority
     *
     * @throws Exception
     */
    public function use(string $middleware, float $priority = 0): void
    {
        //TODO: priority functionality
        if (
            \is_string($middleware) &&
            class_exists($middleware)
        ) {
            $this->nextHandler = &$this->nextHandler->getNextHandlerPointer();
            $this->nextHandler = new MiddlewareRequestHandler($this->container, $middleware);
        } else { // TODO: improve exceptions like in the container (service providers)
            throw new Exception(
                'An item of middlewares configuration should be a string ' .
                'that represents middleware class which implements ' .
                MiddlewareInterface::class . ", got $middleware");
        }
    }

    /**
     * @param array $config
     *
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function boot(array $config): void
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $this->nextHandler = &$this->nextHandler->getNextHandlerPointer();
        $this->nextHandler = $this->getDefaultHandler();
        $response = $this->defaultHandler->handle($request);
        $server = new Server(function () {
        }, $request, $response);
        $server->listen();
    }

    public function get($id)
    {
        // TODO: Implement get() method.
    }
    public function has($id)
    {
        // TODO: Implement has() method.
    }
}
