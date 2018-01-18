<?php

namespace Rosem\Kernel;

use RosemStandards\Kernel\AbstractAppDirectories;

class AppDirectories extends AbstractAppDirectories
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
