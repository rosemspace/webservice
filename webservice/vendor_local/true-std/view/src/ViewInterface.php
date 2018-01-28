<?php

namespace TrueStd\View;

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
}
