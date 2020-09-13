<?php

declare(strict_types=1);

namespace Rosem\Component\App;

use Dotenv\Dotenv;

use function getenv;
use function is_array;

trait EnvTrait
{
    protected Dotenv $env;

    protected bool $envLoaded = false;

    public function loadEnv(): void
    {
        $this->env->load();
        $this->envLoaded = true;
        $this->validateEnv();
    }

    public function hasEnv($id): bool
    {
        return $this->getEnv($id) !== false;
    }

    /**
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

    protected function createEnv($path, $files = '.env'): void
    {
        $this->env = Dotenv::createImmutable($path, $files);
    }

    abstract protected function validateEnv(): void;
}
