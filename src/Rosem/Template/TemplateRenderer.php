<?php

namespace Rosem\Template;

use League\Plates\Engine;
use Rosem\Psr\Template\RenderingExceptionInterface;
use Rosem\Psr\Template\TemplateExceptionInterface;
use Rosem\Psr\Template\TemplateRendererInterface;

class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var Engine
     */
    private $engine;

    public function __construct(string $baseDirectory = '', string $extension = 'phtml')
    {
        $this->engine = Engine::create($baseDirectory, $extension);
    }

    /**
     * Create a new template and render it.
     *
     * @param string $templateName
     * @param array  $data
     * @param array  $attributes
     *
     * @return string
     * @throws RenderingExceptionInterface
     * @throws TemplateExceptionInterface
     */
    public function render(string $templateName, array $data = [], array $attributes = []): string
    {
        return $this->engine->render($templateName, $data, $attributes);
    }

    /**
     * Add a path and optionally its alias.
     *
     * @param string $path
     * @param null|string $alias
     *
     * @return void
     */
    public function addPath(string $path, ?string $alias = null): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->engine->addFolder($alias, $path);
    }

    /**
     * Add data which will be available in each template.
     *
     * @param array $data
     *
     * @return void
     */
    public function addGlobalData(array $data): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->engine->addGlobals($data);
    }

    /**
     * Add data which will be available in the template.
     *
     * @param string $templateName
     * @param array  $data
     *
     * @return void
     */
    public function addTemplateData(string $templateName, array $data): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->engine->addData($data, $templateName);
    }
}
