<?php

namespace Rosem\Kernel;

use Dotenv\Dotenv;
use GraphQL\GraphQL;
use Psr\Container\ContainerExceptionInterface;
use Rosem\Access\Database\Models\{
    User, UserRole
};
use RosemStandards\Kernel\AppInterface;
use True\DI\Container;
use Psr\Container\ContainerInterface;
use True\DI\ReflectionContainer;
use Zend\Diactoros\Server;

class App extends Container implements AppInterface
{
    public static function launch() : void
    {
        try {
            $directories = new WebserviceDirectories;
            $env = new Dotenv($directories->root());
            $env->load();
            $modulesConfig = "{$directories->config()}/modules.php";

            if (is_readable($modulesConfig) && file_exists($modulesConfig)) {
                $modules = include_once $modulesConfig;

                if (is_array($modules)) {
                    $app = new static;
                    $app->delegate(new ReflectionContainer);

                    foreach ($modules as $module => $state) {
                        if ($state && $module !== static::class) {
                            $app->bind($module); // TODO: we shouldn't do an automatic binding
                            $app->get($module);
                        }
                    }

                    $app->boot();
                } else {
                    throw new \Exception('Modules config file is invalid');
                }
            } else {
                throw new \Exception('Modules config file does not exist or not readable');
            }
        } catch (ContainerExceptionInterface $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function __construct()
    {
        parent::__construct();

        $this->instance(ContainerInterface::class, $this);
        $this->alias(ContainerInterface::class, AppInterface::class);
        // SERVER
        $this->bind(\Zend\Diactoros\Server::class);
        // REQUEST
        $this->instance(
            \Psr\Http\Message\ServerRequestInterface::class,
            \Zend\Diactoros\ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            )
        );
        // RESPONSE
        $this->share(
            \Psr\Http\Message\ResponseInterface::class,
            \Zend\Diactoros\Response::class
        );
        // DATABASE
        $this->share(
            \Analogue\ORM\Analogue::class,
            \Analogue\ORM\Analogue::class,
            [[
                'driver'    => getenv('DB_DRIVER'),
                'host'      => getenv('DB_HOST'),
                'database'  => getenv('DB_NAME'),
                'username'  => getenv('DB_USERNAME'),
                'password'  => getenv('DB_PASSWORD'),
                'charset'   => getenv('DB_CHARSET'),
                'collation' => getenv('DB_COLLATION'),
                'prefix'    => getenv('DB_PREFIX'),
            ]]
        );
        // GRAPHQL
        $this->share(
            \TrueStandards\GraphQL\GraphInterface::class,
            \True\GraphQL\Graph::class
        );
    }

    public function boot()
    {
        try {
//        $this->test();
            $this->testGraph();
            $this->make(Server::class, [
                function () {
                },
            ])->listen();
        } catch (ContainerExceptionInterface $e) {
            echo $e->getMessage();
        }
    }










    public function testGraph()
    {
        $graph = $this->get(\TrueStandards\GraphQL\GraphInterface::class);
        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->get(\Psr\Http\Message\ServerRequestInterface::class);


        try {
            $input = $request->getQueryParams(); // GET

            if (! isset($input['query'])) { // POST
                $input = json_decode($request->getBody()->getContents(), true);
            }

            $result = GraphQL::executeQuery(
                $graph->getSchema(),
                $input['query'] ?? null,
                null,
                $this,
                $input['variables'] ?? null,
                $input['operationName'] ?? null
            );
            $output = $result->toArray();
        } catch (\Exception $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        $response = $this->get(\Psr\Http\Message\ResponseInterface::class);
//        $response = new \Zend\Diactoros\Response\JsonResponse(json_encode($output));
        $response->getBody()->write(json_encode($output));
//        header('Content-Type: application/json; charset=UTF-8');
//        echo json_encode($output);
    }

    public function testDB()
    {
        /** @var \Analogue\ORM\System\Mapper $roleMapper */
        /** @var \Analogue\ORM\System\Mapper $userMapper */
        $db = $this->get(\Analogue\ORM\Analogue::class);

        $roleMapper = $db->mapper(UserRole::class);

//        $adminRole = new UserRole('admin');
//        $userRole = new UserRole('user');
//        $roleMapper->store([$adminRole, $userRole]);

        $adminRole = $roleMapper->where('name', 'admin')->first();
        $userMapper = $db->mapper(User::class);
//        $roman = new User('Roman', 'Shevchenko', 'roshe@smile.fr', '123456', $adminRole);
//        $userMapper->store($roman);

        echo $userMapper->where('first_name', 'Roman')->first()->role->name;

        $response = $this->get(\Psr\Http\Message\ResponseInterface::class);
        /**
         * @var \Psr\Http\Message\ResponseInterface $response
         */
        $response->getBody()->write('<h1>' .
            $roleMapper->where('name', 'admin')->first()->name .
            '</h1>');
    }
}
