<?php

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

// Create a DI Container
$container = new Container();

// Register dependencies
$container->set(App\Repositories\GroupRepository::class, function () {
    return new App\Repositories\GroupRepository(); // Adjust constructor if needed
});

$container->set(App\Repositories\UserGroupRepository::class, function () {
    return new App\Repositories\UserGroupRepository(); // Adjust constructor if needed
});

$container->set(App\Services\GroupService::class, function ($container) {
    return new App\Services\GroupService(
        $container->get(App\Repositories\GroupRepository::class), // Inject GroupRepository
        $container->get(App\Repositories\UserGroupRepository::class) // Inject UserGroupRepository
    );
});

$container->set(App\Controllers\GroupController::class, function ($container) {
    return new App\Controllers\GroupController(
        $container->get(App\Services\GroupService::class)
    );
});

// Add other services and controllers similarly
$container->set(App\Services\MessageService::class, function ($container) {
    return new App\Services\MessageService(
        $container->get(App\Repositories\MessageRepository::class),
        $container->get(App\Repositories\UserGroupRepository::class)

    );
});
$container->set(App\Controllers\MessageController::class, function ($container) {
    return new App\Controllers\MessageController(
        $container->get(App\Services\MessageService::class)
    );
});


// Create the Slim App with the container
AppFactory::setContainer($container);
$app = AppFactory::create();

// Middleware
$app->addBodyParsingMiddleware();
$app->add(App\Middlewares\GroupValidationMiddleware::class); // Add validation middleware globally
$app->add(App\Middlewares\MessageValidationMiddleware::class);

// Test Database Route
$app->get('/test-db', function ($request, $response) {
    $pdo = \App\Database::connect();
    $response->getBody()->write('Database connected successfully!');
    return $response;
});

$app->get('/favicon.ico', function ($request, $response) {
    return $response->withStatus(204); // No Content
});

$app->group('/groups', require __DIR__ . '/../src/routes/groupRoutes.php');
$app->group('/messages', require __DIR__ . '/../src/routes/messageRoutes.php');


$app->run();
