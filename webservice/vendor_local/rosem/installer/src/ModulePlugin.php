<?php

namespace True\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ModulePlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        print "hello world";
    }
}
