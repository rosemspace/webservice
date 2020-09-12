<?php

namespace Rosem\Component\Container;

use function file_exists;
use function is_array;
use function is_readable;

trait ConfigFileTrait
{
    /**
     * @param string $filePath
     *
     * @return mixed
     * @throws Exception\ContainerException
     */
    protected static function getConfigurationFromFile(string $filePath): array
    {
        if (file_exists($filePath)) {
            if (is_readable($filePath)) {
                /** @noinspection PhpIncludeInspection */
                $config = include $filePath;

                if (!is_array($config)) {
                    throw new Exception\ContainerException(
                        "$filePath configuration file should return an array"
                    );
                }

                return $config;
            }

            throw new Exception\ContainerException(
                "$filePath configuration file does not readable"
            );
        }

        throw new Exception\ContainerException(
            "$filePath configuration file does not exist"
        );
    }
}
