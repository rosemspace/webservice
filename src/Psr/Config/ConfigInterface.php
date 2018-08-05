<?php

namespace Rosem\Psr\Config;

interface ConfigInterface
{
    public function get(string $key, $default = null);

    public function set(string $key, $value) : void;
}
