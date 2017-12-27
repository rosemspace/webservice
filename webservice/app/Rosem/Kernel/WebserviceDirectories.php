<?php

namespace Rosem\Kernel;

use RosemStandards\Kernel\AbstractWebserviceDirectories;

class WebserviceDirectories extends AbstractWebserviceDirectories
{
    /**
     * @var string
     */
    protected $config;

    public function __construct(?string $publicDirectory = null)
    {
        parent::__construct($publicDirectory);

        $this->config = "$this->root/config";
    }

    public function config()
    {
        return $this->config;
    }
}
