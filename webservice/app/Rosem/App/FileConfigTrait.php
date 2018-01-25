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
    protected static function getConfiguration(string $filePath) : array
    {
        if (file_exists($filePath)) {
            if (is_readable($filePath)) {
                $config = include $filePath;

                if (is_array($config)) {
                    if (empty($config)) {
                        throw new Exception("$filePath configuration file shouldn't be empty");
                    }

                    return $config;
                }

                throw new Exception("$filePath configuration file should return an array");
            }

            throw new Exception("$filePath configuration file does not readable");
        }

        throw new Exception("$filePath configuration file does not exists");
    }
}
