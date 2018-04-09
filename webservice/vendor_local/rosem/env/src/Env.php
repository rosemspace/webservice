<?php

namespace Rosem\Env;

use Dotenv\Dotenv;
use Psrnext\Env\EnvInterface;

class Env implements EnvInterface
{
    /**
     * @var Dotenv
     */
    private $env;

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

    public function load(): void
    {
        $this->env->load();
        $this->validate();
    }

    public function overload(): void
    {
        $this->env->overload();
        $this->validate();
    }

    public function getMode(): string
    {
        return getenv(self::KEY_MODE);
    }
}
