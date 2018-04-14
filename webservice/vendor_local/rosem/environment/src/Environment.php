<?php

namespace Rosem\Environment;

use Dotenv\Dotenv;
use Psrnext\Environment\AbstractEnvironment;

class Environment extends AbstractEnvironment
{
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
        $this->env->required(self::KEY_MODE)->allowedValues([
            self::MODE_DEVELOPMENT,
            self::MODE_MAINTENANCE,
            self::MODE_PRODUCTION,
            self::MODE_STAGING,
            self::MODE_TESTING,
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
        return $this->get(self::KEY_MODE);
    }
}
