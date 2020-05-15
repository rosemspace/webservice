<?php

namespace Rosem\Component\App;

use Dotenv\Dotenv;

use function getenv;
use function is_array;

trait EnvTrait
{
    /**
     * @var Dotenv
     */
    protected Dotenv $env;

    /**
     * @var bool
     */
    protected bool $envLoaded = false;

    protected function createEnv($path, $files = '.env'): void
    {
        $this->env = Dotenv::createImmutable($path, $files);
    }

    public function loadEnv(): void
    {
        $this->env->load();
        $this->envLoaded = true;
        $this->validateEnv();
    }

    public function hasEnv($id): bool
    {
        return false !== $this->getEnv($id);
    }

    /**
     * @param string|null $id
     *
     * @return string[]|string|bool|null
     */
    public function getEnv(?string $id)
    {
        $env = getenv($id);

        if (is_array($env)) {
            return $_ENV;
        }

        switch ($env) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
            case false:
                return null;
            default:
                return $env;
        }
    }

    abstract protected function validateEnv(): void;
}
