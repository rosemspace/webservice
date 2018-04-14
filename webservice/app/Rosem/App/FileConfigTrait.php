<?php

namespace Rosem\App;

use Exception;

trait FileConfigTrait
{
    /**
     * @param string $filePath
     *
     * @return mixed
     * @throws Exception
     */
    protected static function getConfiguration(string $filePath): array
    {
        if (file_exists($filePath)) {
            if (is_readable($filePath)) {
                $config = include $filePath;

                if (\is_array($config)) {
                    return $config;
                }

                throw new Exception("$filePath configuration file should return an array");
            }

            throw new Exception("$filePath configuration file does not readable");
        }

        throw new Exception("$filePath configuration file does not exists");
    }
}
