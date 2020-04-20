<?php

namespace Rosem\Component\App;

use Dotenv\Dotenv;

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

    protected function createEnv($path, $file = '.env'): void
    {
        $this->env = Dotenv::createImmutable($path, $file);
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
     * @return string[]|string|boolean|null
     */
    public function getEnv(?string $id)
    {
        $env = getenv($id);

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
