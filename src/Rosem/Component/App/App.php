<?php

namespace Rosem\Component\App;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Component\Container\{
    AbstractContainer,
    ConfigurationContainer,
    ServiceContainer
};
use Rosem\Contract\App\{
    AppEnv,
    AppEnvVar,
    AppInterface
};
use Rosem\Contract\Debug\InspectableInterface;
use Rosem\Contract\Http\Server\EmitterInterface;

class App implements AppInterface, InspectableInterface
{
    use EnvTrait;

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
     * @var string
     */
    private string $locale = 'en-us';

    /**
     * The application root directory.
     *
     * @var string
     */
    private string $rootDir;

    /**
     * Check if the application is allowed to debug.
     *
     * @var bool
     */
    private bool $debug;

    /**
     * The application container.
     *
     * @var AbstractContainer
     */
    protected AbstractContainer $container;

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
//        parent::__construct($config['providers'] ?? []);

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

//        $this->delegate(ConfigurationContainer::fromArray($config));
        $this->container = ConfigurationContainer::fromArray($config);
        $this->container->delegate(
            new ServiceContainer($config['providers'] ?? [], $this->container)
        );
        $this->rootDir = $config['root'];
        //$filePath //$path, $file = '.env'
        $this->createEnv($this->rootDir, $config['envFile'] ?? '.env');
        $exceptionThrown = false;
        //todo env variables may be set before application initialization
        //$this->environment = $this->getEnv(self::ENV_KEY) ?? '';
        //$this->debug = $this->isEnvironment(EnvEnum::DEVELOPMENT);

        try {
            $this->loadEnv();
        } catch (Exception $exception) {
            $exceptionThrown = true;
        }

        $this->environment = $this->getEnv(AppEnvVar::ENV_KEY) ?? '';
        $debug = $this->getEnv(AppEnvVar::DEBUG_KEY);
        $this->debug = $debug !== 'auto'
            ? $debug
            : $this->isEnvironment(AppEnv::DEVELOPMENT);

        if ($this->envLoaded) {
            $this->version = $this->getEnv(AppEnvVar::VERSION_KEY) ?? '';
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
    }

    /**
     * @inheritDoc
     */
    public function run(): bool
    {
//        $app = $this;
//        $this->container->set(AppInterface::class, static fn() => $app);

        return $this->container->get(EmitterInterface::class)->emit(
            $this->container->get(RequestHandlerInterface::class)->handle(
                $this->container->get(ServerRequestInterface::class)
            )
        );
    }

    protected function validateEnv(): void
    {
        $this->env->required(AppEnvVar::VERSION_KEY)->notEmpty();
        $this->env->required(AppEnvVar::ENV_KEY)->allowedValues(
            [
                AppEnv::LOCAL,
                AppEnv::DEMO,
                AppEnv::DEVELOPMENT,
                AppEnv::TEST,
                AppEnv::ACCEPTANCE,
                AppEnv::PRODUCTION,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @inheritDoc
     */
    public function isEnvironment(string $env): bool
    {
        return $this->environment === $env;
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @inheritDoc
     */
    public function isLocale(string $locale): bool
    {
        return $locale === $this->locale;
    }

    /**
     * @inheritDoc
     */
    public function isAllowedToDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function isDownForMaintenance(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDemoVersion(): bool
    {
        return $this->getEnv(AppEnvVar::ENV_KEY) === AppEnv::DEMO;
    }

    /**
     * @inheritDoc
     */
    public function isRunningInConsole(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * @inheritDoc
     */
    public function inspect(): array
    {
        // TODO: Implement inspect() method.
        return [];
    }
}
