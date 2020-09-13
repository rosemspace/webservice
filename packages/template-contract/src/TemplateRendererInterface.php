<?php

declare(strict_types=1);

/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Template;

/**
 * View renderer renders a given template.
 */
interface TemplateRendererInterface
{
    /**
     * Create a new template and render it.
     *
     * @throws RenderingExceptionInterface
     * @throws TemplateExceptionInterface
     */
    public function render(string $templateName, array $data = [], array $attributes = []): string;

    /**
     * Add a path and optionally its alias.
     */
    public function addPath(string $path, ?string $alias = null): void;

    /**
     * Add data which will be available in each template.
     */
    public function addGlobalData(array $data): void;

    /**
     * Add data which will be available in the template.
     */
    public function addTemplateData(string $templateName, array $data): void;
}
