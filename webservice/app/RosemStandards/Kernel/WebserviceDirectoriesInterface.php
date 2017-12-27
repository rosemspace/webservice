<?php

namespace RosemStandards\Kernel;

interface WebserviceDirectoriesInterface
{
    public function root() : string;

    public function src() : string;

    public function public() : string;
}
