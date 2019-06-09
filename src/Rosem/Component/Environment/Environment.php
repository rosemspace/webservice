<?php

namespace Rosem\Component\Environment;

use Dotenv\Dotenv;
use Rosem\Contract\Environment\{
    AbstractEnvironment,
    EnvironmentMode
};

class Environment extends AbstractEnvironment
{
    /**
     * Environment application mode key.
     */
    public const MODE = 'APP_MODE';

    public const ROOT_DIRECTORY = 'ROOT_DIRECTORY';

    public const PUBLIC_DIRECTORY = 'PUBLIC_DIRECTORY';

    public const MEDIA_DIRECTORY = 'MEDIA_DIRECTORY';

    public const TEMP_DIRECTORY = 'TEMP_DIRECTORY';

    public const CACHE_DIRECTORY = 'CACHE_DIRECTORY';

    public const LOG_DIRECTORY = 'LOG_DIRECTORY';

    public const SESSION_DIRECTORY = 'SESSION_DIRECTORY';

    public const UPLOAD_DIRECTORY = 'UPLOAD_DIRECTORY';

    public const EXPORT_DIRECTORY = 'EXPORT_DIRECTORY';

    /**
     * @var Dotenv
     */
    private $env;

    /**
     * @var bool|array
     */
    private $loaded = false;

    public function __construct($path, $file = '.env')
    {
        $this->env = new Dotenv($path, $file);
    }

    private function validate(): void
    {
        $this->env->required(self::MODE)->allowedValues([
            EnvironmentMode::MAINTENANCE,
            EnvironmentMode::DEMO,
            EnvironmentMode::DEVELOPMENT,
            EnvironmentMode::STAGING,
            EnvironmentMode::PRODUCTION,
        ]);
    }

    public function load(): array
    {
        if (!$this->loaded) {
            $this->loaded = $this->env->load();
            $this->validate();
        }

        return $this->loaded;
    }

    public function overload(): void
    {
        $this->env->overload();
        $this->validate();
    }

    public function has(string $id): bool
    {
        return false !== $this->get($id);
    }

    public function get(string $id): string
    {
        $this->load();

        return getenv($id);
    }

    public function getAppMode(): string
    {
        return $this->get(self::MODE);
    }

    public function getRootDirectory(): string
    {
        return $this->get(self::ROOT_DIRECTORY);
    }

    public function getPublicDirectory(): string
    {
        return $this->get(self::PUBLIC_DIRECTORY);
    }

    public function getMediaDirectory(): string
    {
        return $this->get(self::MEDIA_DIRECTORY);
    }

    public function getTempDirectory(): string
    {
        return $this->get(self::TEMP_DIRECTORY);
    }

    public function getCacheDirectory(): string
    {
        return $this->get(self::CACHE_DIRECTORY);
    }

    public function getLogDirectory(): string
    {
        return $this->get(self::LOG_DIRECTORY);
    }

    public function getSessionDirectory(): string
    {
        return $this->get(self::SESSION_DIRECTORY);
    }

    public function getUploadDirectory(): string
    {
        return $this->get(self::UPLOAD_DIRECTORY);
    }

    public function getExportDirectory(): string
    {
        return $this->get(self::EXPORT_DIRECTORY);
    }
}
