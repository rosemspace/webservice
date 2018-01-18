<?php

namespace RosemStandards\Kernel;

interface AppDirectoriesInterface
{
    public function root() : string;

    public function src() : string;

    public function public() : string;
}
