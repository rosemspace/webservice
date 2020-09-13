<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use Rosem\Component\Container\Exception\ContainerException;

use function file_exists;
use function is_array;
use function is_readable;

trait ConfigFileTrait
{
    /**
     * @return mixed
     * @throws ContainerException
     */
    protected static function getConfigurationFromFile(string $filePath): array
    {
        if (file_exists($filePath)) {
            if (is_readable($filePath)) {
                /** @noinspection PhpIncludeInspection */
                $config = include $filePath;

                if (! is_array($config)) {
                    throw new ContainerException("${filePath} configuration file should return an array");
                }

                return $config;
            }

            throw new ContainerException("${filePath} configuration file does not readable");
        }

        throw new ContainerException("${filePath} configuration file does not exist");
    }
}
