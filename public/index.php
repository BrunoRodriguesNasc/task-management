<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use App\Middleware\ErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

// Database configuration
$dbConfig = [
    'host' => 'task-api-postgres',
    'dbname' => 'task_db',
    'user' => 'task_user',
    'password' => 'task_password',
    'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
];

$container->set('db', function () use ($dbConfig) {
    return new PDO(
        "pgsql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
        $dbConfig['user'],
        $dbConfig['password'],
        $dbConfig['options']
    );
});

$container->set(\App\Models\Task::class, function ($container) {
    return new \App\Models\Task($container->get('db'));
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = new ErrorHandler();
$errorMiddleware->setDefaultErrorHandler($errorHandler);

$routes = require __DIR__ . '/../src/Routes/api.php';
$routes($app);

$app->get('/', function ($request, $response) {
    return ApiResponse::success('API is running');
});

$app->get('/api-docs', function ($request, $response) {
    return $response->withRedirect('/api-docs');
});

$app->run(); 