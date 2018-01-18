<?php

namespace Rosem\Kernel;

use Dotenv\Dotenv;
use Exception;
use GraphQL\GraphQL;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Rosem\Access\Database\Models\{
    User, UserRole
};
use RosemStandards\Kernel\AppInterface;
use True\DI\Container;
use Zend\Diactoros\Server;

class App extends Container //implements AppInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->instance(ContainerInterface::class, $this)->commit();
        $this->alias(ContainerInterface::class, AppInterface::class);
    }

    public function boot(string $configFileName)
    {
        $directories = new AppDirectories; // TODO: get from the container
        $env = new Dotenv($directories->root());
        $env->load();
        $configFilePath = "{$directories->config()}/$configFileName";

        if (is_readable($configFilePath) && file_exists($configFilePath)) {
            $config = require_once($configFilePath);

            if (is_array($config)) {
                foreach ($config as $key => $data) {
                    $this->instance($key, $data)->commit();
                }
            } else {
                throw new Exception('App config file is invalid');
            }
        } else {
            throw new Exception('App config file does not exist or not readable');
        }

        try {
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
        } catch (Exception $e) {
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
