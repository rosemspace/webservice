<?php

use Rosem\Component\App\App;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Rosem application instance
| which serves as the "glue" for all the components of Rosem, and is
| the IoC container for the system binding all of the various parts.
|
*/
try {
    $app = new App(include __DIR__ . '/config/app.php');
    $app->get(\Rosem\Contract\Http\Server\MiddlewareRunnerInterface::class)->run();
} catch (Exception $exception) {
    echo $exception->getMessage();
}

//    $entityManager = $app->get(\Doctrine\ORM\EntityManager::class);
//    $newUser = new \Rosem\Access\Entity\User;
//    $newUser->setEmail('roshe@smile.fr');
//    $entityManager->persist($newUser);
//    $entityManager->flush();
//} catch (\Exception $e) {
//    echo $e->getMessage();
//}
//$g = $app->get(\Psrnext\GraphQL\GraphInterface::class);
