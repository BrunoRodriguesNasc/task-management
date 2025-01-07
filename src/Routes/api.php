<?php

use App\Controllers\TaskController;
use Slim\Routing\RouteCollectorProxy;

return function ($app) {
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/tasks', [TaskController::class, 'getTasks']);
        $group->get('/tasks/{id}', [TaskController::class, 'getTaskById']);
        $group->post('/tasks', [TaskController::class, 'createTask']);
        $group->put('/tasks/{id}', [TaskController::class, 'updateTask']);
        $group->delete('/tasks/{id}', [TaskController::class, 'deleteTask']);
        
    });
}; 