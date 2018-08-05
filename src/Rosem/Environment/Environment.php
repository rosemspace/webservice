<?php

namespace Rosem\Environment;

use Dotenv\Dotenv;
use Rosem\Psr\Environment\{
    AbstractEnvironment, EnvironmentMode
};

class Environment extends AbstractEnvironment
{
    /**
     * Environment config key.
     */
    public const CONFIG_KEY = 'APP_ENV';

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
        $this->env->required(self::CONFIG_KEY)->allowedValues([
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

    public function get(string $key, $localOnly = false): string
    {
        return getenv($key, $localOnly);
    }

    public function getMode(): string
    {
        return $this->get(self::CONFIG_KEY);
    }
}
