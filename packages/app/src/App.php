<?php

declare(strict_types=1);

namespace Rosem\Component\App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Component\Container\Exception\ContainerException;
use Rosem\Component\Container\{
    AbstractContainer,
    ConfigurationContainer,
    ServiceContainer
};
use Rosem\Contract\App\{
    AppEnv,
    AppEnvKey,
    AppInterface
};
use Rosem\Contract\Debug\InspectableInterface;
use Rosem\Contract\Http\Server\EmitterInterface;
use Throwable;

use function dirname;
use function preg_replace;

class App implements AppInterface, InspectableInterface
{
    use EnvTrait;

    /**
     * The application container.
     */
    protected AbstractContainer $container;

    /**
     * The application version.
     */
    private string $version;

    /**
     * The application environment.
     */
    private string $environment;

    private string $locale = 'en-us';

    /**
     * The application root directory.
     */
    private string $rootDir;

    /**
     * Check if the application is allowed to debug.
     */
    private bool $debug;

    /**
     * App constructor.
     *
     * @throws ContainerException
     * @throws Throwable
     */
    public function __construct(array $config)
    {
        if (isset($config['root'])) {
            $this->rootDir = $config['root'];
        } else {
            $config['root'] = $this->getRootDir();
        }

        $this->container = ConfigurationContainer::fromArray($config);
        $this->container->delegate(ServiceContainer::fromArray($config['providers'] ?? []));
        //$filePath //$path, $file = '.env'
        $this->createEnv($this->rootDir, $config['envFile'] ?? '.env');
        $exceptionThrown = false;
        //todo env variables may be set before application initialization

        try {
            $this->loadEnv();
        } catch (Throwable $exception) {
            $exceptionThrown = true;
        }

        $this->environment = $this->getEnv(AppEnvKey::ENV) ?? '';
        $debug = $this->getEnv(AppEnvKey::DEBUG);
        //var_dump(getenv());
        $this->debug = $debug === 'auto'
            ? $this->environment === AppEnv::DEVELOPMENT
            : (bool)$debug;

        if ($this->envLoaded) {
            $this->version = $this->getEnv(AppEnvKey::VERSION) ?? '';
        }

        if ($this->environment && $this->environment !== AppEnv::PRODUCTION && $this->debug) {
            ini_set('display_errors', 'true');
            ini_set('display_startup_errors', 'true');
            ini_set('scream.enabled', 'true');
            ini_set('xdebug.scream', 'true');
            error_reporting(E_ALL);

            if ($exceptionThrown) {
                throw $exception;
            }
        } elseif ($exceptionThrown) {
            //todo log the exception
            //todo show maintenance
            echo 'Failed load .env';
            exit(1);
        }
    }

    public function run(): bool
    {
        return $this->container->get(EmitterInterface::class)->emit(
            $this->container->get(RequestHandlerInterface::class)->handle(
                $this->container->get(ServerRequestInterface::class)
            )
        );
    }

    public function getRootDir(int $levelsUp = 1): string
    {
        //todo html escape
        if (!isset($this->rootDir)) {
            if (PHP_SAPI === 'cli') {
                $pwd = $_SERVER['PWD'] ?? getcwd();
                $scriptName = $_SERVER['SCRIPT_NAME'];
                /** @noinspection RegExpRedundantEscape */
                // Go above "public/index.php" or "bin/rosem" file
                $this->rootDir = dirname(
                    str_starts_with($scriptName, $pwd)
                        // Absolute path: "/var/www/public/index.php" run as CLI
                        ? $scriptName
                        // Relative path: "bin/rosem" run as CLI
                        : preg_replace(
                            '/\\' . DIRECTORY_SEPARATOR . '\.?$/',
                            '',
                            $pwd
                        ) . DIRECTORY_SEPARATOR .
                        preg_replace(
                            '/^\.?\\' . DIRECTORY_SEPARATOR . '|\\' . DIRECTORY_SEPARATOR . '\.?$/',
                            '',
                            $_SERVER['SCRIPT_NAME']
                        ),
                    $levelsUp + 1
                );
            } else {
                // Go above "public" directory ("/var/www/public")
                $this->rootDir = dirname($_SERVER['DOCUMENT_ROOT'] ?? getcwd(), $levelsUp);
            }
        }

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

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isAllowedToDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @todo
     */
    public function isDownForMaintenance(): bool
    {
        return false;
    }

    public function isDemoVersion(): bool
    {
        return $this->getEnv(AppEnvKey::ENV) === AppEnv::DEMO;
    }

    public function isRunningInConsole(): bool
    {
        return PHP_SAPI === 'cli';
    }

    public function inspect(): array
    {
        // TODO: Implement inspect() method.
        return [];
    }

    protected function validateEnv(): void
    {
        $this->env->required(AppEnvKey::VERSION)->notEmpty();
        $this->env->required(AppEnvKey::ENV)->allowedValues(
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
}
