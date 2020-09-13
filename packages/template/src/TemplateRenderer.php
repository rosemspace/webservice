<?php

declare(strict_types=1);

namespace Rosem\Component\Template;

use League\Plates\Engine;
use Rosem\Contract\Template\RenderingExceptionInterface;
use Rosem\Contract\Template\TemplateExceptionInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

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
     * @throws RenderingExceptionInterface
     * @throws TemplateExceptionInterface
     */
    public function render(string $templateName, array $data = [], array $attributes = []): string
    {
        return $this->engine->render($templateName, $data, $attributes);
    }

    /**
     * Add a path and optionally its alias.
     */
    public function addPath(string $path, ?string $alias = null): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->engine->addFolder($alias, $path);
    }

    /**
     * Add data which will be available in each template.
     */
    public function addGlobalData(array $data): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->engine->addGlobals($data);
    }

    /**
     * Add data which will be available in the template.
     */
    public function addTemplateData(string $templateName, array $data): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->engine->addData($data, $templateName);
    }
}
