<?php

namespace Rosem\Route;

interface ParserInterface
{
    public function parse(string $routePattern): array;
}
