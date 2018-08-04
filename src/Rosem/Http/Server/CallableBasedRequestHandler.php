<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use UnexpectedValueException;
use function call_user_func_array;
use function is_array;
use function is_object;
use function is_scalar;
use function is_string;
use function key;
use function method_exists;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;
use function reset;

/**
 * Simple class to execute callables as request handlers.
 */
class CallableBasedRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var callable
     */
    protected $callable;

    public function __construct(ContainerInterface $container, callable $callable)
    {
        $this->container = $container;
        $this->responseFactory = $container->get(ResponseFactoryInterface::class);
        $this->callable = $callable;
    }

    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->execute($this->callable, $request);
    }

    /**
     * Execute the callable.
     *
     * @param array|callable $callable
     * @param object         ...$arguments
     *
     * @return ResponseInterface
     * @throws Exception
     */
    protected function execute($callable, ...$arguments): ResponseInterface
    {
        ob_start();
        $level = ob_get_level();

        try {
            if (is_array($callable) && is_string(reset($callable))) {
                $callable[key($callable)] = $this->container->get(reset($callable));
            }

            $return = call_user_func_array($callable, $arguments);

            if ($return instanceof ResponseInterface) {
                $response = $return;
                $return = '';
            } elseif (
                null === $return
                || is_scalar($return)
                || (is_object($return) && method_exists($return, '__toString'))
            ) {
                $response = $this->responseFactory->createResponse();
            } else {
                throw new UnexpectedValueException(
                    'The value returned must be scalar or an object with __toString method'
                );
            }

            while (ob_get_level() >= $level) {
                $return = ob_get_clean() . $return;
            }

            $body = $response->getBody();

            if ($return !== '' && $body->isWritable()) {
                $body->write($return);
            }

            return $response;
        } catch (Exception $exception) {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}
