<?php

namespace Psrnext\View;

interface ViewInterface
{
    /**
     * Create a new template and render it.
     *
     * @param  string $templateName
     * @param  array  $data
     * @param array   $attributes
     *
     * @return string
     */
    public function render(string $templateName, array $data = [], array $attributes = []): string;

    /**
     * Add an alias for directory.
     *
     * @param string $alias
     * @param string $path
     *
     * @return void
     */
    public function addDirectoryAlias(string $alias, string $path) : void;

    /**
     * Add data which will be available in each template.
     *
     * @param array $data
     *
     * @return void
     */
    public function addData(array $data) : void;
}
