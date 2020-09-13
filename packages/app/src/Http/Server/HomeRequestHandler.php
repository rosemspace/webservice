<?php

namespace Rosem\Component\App\Http\Server;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

class HomeRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * @var TemplateRendererInterface
     */
    protected TemplateRendererInterface $templateRenderer;

    /**
     * @var array
     */
    protected array $config;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface       $responseFactory
     * @param TemplateRendererInterface|null $templateRenderer
     * @param array                          $config
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ?TemplateRendererInterface $templateRenderer,
        array $config = []
    ) {
        $this->responseFactory = $responseFactory;
        $this->config = $config;

        if ($templateRenderer !== null) {
            $this->templateRenderer = $templateRenderer;
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        if (!isset($this->templateRenderer)) {
            return $response;
        }

        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->templateRenderer->render('app::index', $this->config);

            if ($viewString) {
                $body->write($viewString);
            }
        }

        return $response;
    }
}
