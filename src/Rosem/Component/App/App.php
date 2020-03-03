<?php

namespace Rosem\Component\App;

use Exception;
use Rosem\Component\Container\ConfigurationContainer;
use Rosem\Component\Container\ServiceContainer;
use Rosem\Contract\App\{
    AppInterface,
    EnvEnum
};
use Rosem\Contract\Debug\InspectableInterface;
use Rosem\Contract\Http\Server\MiddlewareRunnerInterface;

class App extends ServiceContainer implements AppInterface, InspectableInterface
{
    use EnvTrait;

    /**
     * The application environment key.
     */
    public const ENV_KEY = 'APP_ENV';

    /**
     * The application version key.
     */
    public const VERSION_KEY = 'APP_VERSION';

    /**
     * The application debug key.
     */
    public const DEBUG_KEY = 'APP_DEBUG';

    /**
     * The application root directory.
     *
     * @var string
     */
    private string $rootDir;

    /**
     * The application version.
     *
     * @var string
     */
    private string $version;

    /**
     * The application environment.
     *
     * @var string
     */
    private string $environment;

    /**
     * Check if the application is allowed to debug.
     *
     * @var bool
     */
    private bool $debug;

    /**
     * The application configuration.
     *
     * @var ConfigurationContainer
     */
    protected ConfigurationContainer $configuration;

    /**
     * App constructor.
     *
     * @param array $config
     *
     * @throws \Rosem\Component\Container\Exception\ContainerException
     * @throws \Dotenv\Exception\InvalidPathException
     * @throws \Dotenv\Exception\InvalidFileException
     * @throws \Dotenv\Exception\ValidationException
     */
    public function __construct(array $config)
    {
        parent::__construct($config['providers'] ?? []);

        if (!isset($config['root'])) {
            // todo vendor/rosem/app - 3
            $rootDir = dirname(__DIR__, 4);

            if ($rootDir === '.') {
                $rootDir = !empty($_SERVER['DOCUMENT_ROOT'])
                    ? dirname($_SERVER['DOCUMENT_ROOT'])
                    : getcwd();
            }

            $config['root'] = $rootDir;
        }

        $this->rootDir = $config['root'];
        //$filePath //$path, $file = '.env'
        $this->createEnv($this->rootDir, $config['envFile'] ?? '.env');
        $exceptionThrown = false;
        //todo env variables may be set before application initialization
        //$this->environment = $this->getEnv(static::ENV_KEY) ?? '';
        //$this->debug = $this->isEnvironment(EnvEnum::DEVELOPMENT);

        try {
            $this->loadEnv();
        } catch (Exception $exception) {
            $exceptionThrown = true;
        }

        $this->environment = $this->getEnv(static::ENV_KEY) ?? '';
        $debug = $this->getEnv(static::DEBUG_KEY);
        $this->debug = $debug !== 'auto'
            ? $debug
            : $this->isEnvironment(EnvEnum::DEVELOPMENT);

        if ($this->envLoaded) {
            $this->version = $this->getEnv(static::VERSION_KEY) ?? '';
        }

        if ($this->debug) {
            ini_set('display_errors', 'true');
            ini_set('display_startup_errors', 'true');
            error_reporting(E_ALL);

            if ($exceptionThrown) {
                throw $exception;
            }
        } elseif ($exceptionThrown) {
            //todo log the exception
            //todo show maintenance
            exit(1);
        }

        $this->delegate(ConfigurationContainer::fromArray($config));
    }

    public function run(): bool
    {
        return $this->get(EmitterInterface::class)->emit(
            $this->get(MiddlewareCollectorInterface::class)->handle(
                $this->get(ServerRequestInterface::class)
            )
        );
    }

    protected function validateEnv(): void
    {
        $this->env->required(static::VERSION_KEY)->notEmpty();
        $this->env->required(static::ENV_KEY)->allowedValues(
            [
                EnvEnum::LOCAL,
                EnvEnum::MAINTENANCE,
                EnvEnum::DEMO,
                EnvEnum::DEVELOPMENT,
                EnvEnum::TEST,
                EnvEnum::ACCEPTANCE,
                EnvEnum::PRODUCTION,
            ]
        );
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @param string[]|string $env
     *
     * @return bool
     */
    public function isEnvironment($env): bool
    {
        return is_array($env) ? in_array($this->environment, $env, true) : $this->environment === $env;
    }

    /**
     * @return bool
     */
    public function isAllowedToDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return bool
     * @todo
     */
    public function isDownForMaintenance(): bool
    {
        return false;
    }

    /**
     * @return bool
     * @todo
     */
    public function isDemoVersion(): bool
    {
        return false;
    }

    public function inspect(): array
    {
        // TODO: Implement inspect() method.
        return [];
    }
}
