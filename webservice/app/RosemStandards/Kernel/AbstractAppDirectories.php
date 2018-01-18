<?php

namespace RosemStandards\Kernel;

abstract class AbstractAppDirectories implements AppDirectoriesInterface
{
    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $src;

    /**
     * @var string
     */
    private $public;

    public function __construct(?string $publicDirectory = null)
    {
        $this->public = $publicDirectory ?: getcwd();
        $this->root = realpath($this->public . '/..');
        $this->src = $this->root . '/app';
    }

    public function root() : string
    {
        return $this->root;
    }

    public function src() : string
    {
        return $this->src;
    }

    public function public() : string
    {
        return $this->public;
    }
}
