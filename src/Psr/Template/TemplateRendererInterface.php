<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Psr\Template;

/**
 * View renderer renders a given template.
 */
interface TemplateRendererInterface
{
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
    public function render(string $templateName, array $data = [], array $attributes = []): string;

    /**
     * Add a path and optionally its alias.
     *
     * @param string      $path
     * @param null|string $alias
     *
     * @return void
     */
    public function addPath(string $path, ?string $alias = null): void;

    /**
     * Add data which will be available in each template.
     *
     * @param array $data
     *
     * @return void
     */
    public function addGlobalData(array $data): void;

    /**
     * Add data which will be available in the template.
     *
     * @param string $templateName
     * @param array  $data
     *
     * @return void
     */
    public function addTemplateData(string $templateName, array $data): void;
}
