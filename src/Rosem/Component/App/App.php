<?php

namespace Rosem\Component\App;

use Rosem\Component\Container\ConfigurationContainer;
use Rosem\Component\Container\ServiceContainer;
use Rosem\Contract\App\AppInterface;
use Rosem\Contract\App\EnvEnum;

class App extends ServiceContainer implements AppInterface
{
    use EnvTrait;

    /**
     * The application environment key.
     */
    public const APP_ENV = 'APP_ENV';

    /**
     * The application environment.
     *
     * @var string
     */
    private string $appEnv;

    protected ConfigurationContainer $configuration;

    public function __construct(array $config)
    {
        parent::__construct($config['serviceProviders']);

        //$filePath //$path, $file = '.env'
        $this->createEnv($config['root'], $config['envFile'] ?? '.env');
        $this->loadEnv();
        $this->delegate(ConfigurationContainer::fromArray($config));
    }

    protected function validateEnv(): void
    {
        $this->env->required(self::APP_ENV)->allowedValues([
            EnvEnum::LOCAL,
            EnvEnum::MAINTENANCE,
            EnvEnum::DEMO,
            EnvEnum::DEVELOPMENT,
            EnvEnum::TEST,
            EnvEnum::ACCEPTANCE,
            EnvEnum::PRODUCTION,
        ]);
    }

    public function getEnvironment(): string
    {
        if (!isset($this->appEnv)) {
            $this->appEnv = $this->getEnv(static::APP_ENV);
        }

        return $this->appEnv;
    }

    /**
     * @param array|string[] $env
     *
     * @return bool
     */
    public function isEnvironment($env): bool
    {
        return is_array($env) ? in_array($this->appEnv, $env, true) : $this->appEnv === $env;
    }

    /**
     * @return bool
     * @throws \Rosem\Component\Container\Exception\ContainerException
     * @throws \Rosem\Component\Container\Exception\NotFoundException
     */
    public function onDebugging(): bool
    {
        return $this->configuration->get('debug');
    }
}
