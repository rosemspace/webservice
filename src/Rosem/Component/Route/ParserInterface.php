<?php

namespace Rosem\Component\Route;

interface ParserInterface
{
    public function parse(string $routePattern): array;
}
