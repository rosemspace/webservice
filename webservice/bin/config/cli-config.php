<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once __DIR__ . '/../../bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $app->get('db');

return ConsoleRunner::createHelperSet($entityManager);
