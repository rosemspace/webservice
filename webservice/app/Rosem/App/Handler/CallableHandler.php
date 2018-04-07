<?php

namespace Rosem\App\Handler;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Http\Factory\ResponseFactory;

/**
 * Simple class to execute callables as request handlers.
 */
class CallableHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var callable
     */
    protected $callable;

    public function __construct(ResponseFactory $responseFactory, callable $callable)
    {
        $this->responseFactory = $responseFactory;
        $this->callable = $callable;
    }

    /**
     * Handle the request and return a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->execute($this->callable, [$request]);
    }

    /**
     * Execute the callable.
     * @param callable $callable
     * @param array $arguments
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function execute(callable $callable, array $arguments = []): ResponseInterface
    {
        ob_start();
        $level = ob_get_level();

        try {
            $return = \call_user_func_array($callable, $arguments);

            if ($return instanceof ResponseInterface) {
                $response = $return;
                $return = '';
            } elseif (
                null === $return
                || is_scalar($return)
                || (\is_object($return) && method_exists($return, '__toString'))
            ) {
                $response = $this->responseFactory->createResponse();
            } else {
                throw new \UnexpectedValueException(
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
        } catch (\Exception $exception) {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}
