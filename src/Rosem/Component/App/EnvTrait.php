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
     * @var array|null
     */
    private ?array $loadedEnv = null;

    protected function createEnv($path, $file = '.env'): void
    {
        $this->env = new Dotenv($path, $file);
    }

    public function loadEnv(): array
    {
        if ($this->loadedEnv === null) {
            $this->loadedEnv = $this->env->load();
            $this->validateEnv();
        }

        return $this->loadedEnv;
    }

    public function overloadEnv(): void
    {
        $this->env->overload();
        $this->validateEnv();
    }

    public function hasEnv($id): bool
    {
        return false !== $this->get($id);
    }

    /**
     * @param string|null $id
     *
     * @return array|string|null
     */
    public function getEnv(?string $id)
    {
        $this->loadEnv();

        return getenv($id) ?: null;
    }

    abstract protected function validateEnv(): void;
}
