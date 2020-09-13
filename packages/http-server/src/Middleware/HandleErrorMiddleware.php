<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Middleware;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};
use Rosem\Contract\Template\TemplateRendererInterface;

use function mb_strtoupper;

/**
 * Class HandleErrorMiddleware.
 */
class HandleErrorMiddleware implements MiddlewareInterface
{
    protected ResponseFactoryInterface $responseFactory;

    protected ?TemplateRendererInterface $view;

    protected array $config;

    /**
     * ClientErrorMiddleware constructor.
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ?TemplateRendererInterface $view = null,
        array $config = []
    ) {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->config = $config;
    }

    public static function fromContainer(ContainerInterface $container): MiddlewareInterface
    {
        return new self(
            $container->get(ResponseFactoryInterface::class),
            $container->has(TemplateRendererInterface::class)
                ? $container->get(TemplateRendererInterface::class)
                : null
        );
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        switch ($response->getStatusCode()) {
            case StatusCode::STATUS_NOT_FOUND:
                $this->attachHtmlToResponse($response, StatusCode::STATUS_NOT_FOUND);

                break;
            case StatusCode::STATUS_METHOD_NOT_ALLOWED:
                $this->config['requestMethod'] = mb_strtoupper($request->getMethod());
                $this->config['allowedMethods'] = $response->getHeader('Access-Control-Allow-Methods')[0] ?? '';
                $this->attachHtmlToResponse($response, StatusCode::STATUS_METHOD_NOT_ALLOWED);

                break;
            case StatusCode::STATUS_INTERNAL_SERVER_ERROR:
                $this->attachHtmlToResponse($response, StatusCode::STATUS_INTERNAL_SERVER_ERROR);

                break;
        }

        return $response;
    }

    public function attachHtmlToResponse(ResponseInterface $response, int $statusCode): void
    {
        // TODO add isApi check to attach JSON instead of HTML
        if ($this->view === null) {
            return;
        }

        $body = $response->getBody();

        if (! $body->isWritable()) {
            return;
        }

        $viewString = $this->view->render("error::${statusCode}", $this->config);

        if (empty($viewString)) {
            return;
        }

        $body->write($viewString);
    }
}
