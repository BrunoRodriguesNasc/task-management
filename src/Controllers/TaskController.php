<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Task;

class TaskController
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getTasks(Request $request, Response $response)
    {
        $tasks = $this->task->getAllTasks();
        $response->getBody()->write(json_encode($tasks));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createTask(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $task = $this->task->createTask($data);
        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json')
                       ->withStatus(201);
    }

    public function updateTask(Request $request, Response $response, array $args)
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $task = $this->task->updateTask($id, $data);
        
        $responseData = [
            'success' => true,
            'data' => $task
        ];
        
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteTask(Request $request, Response $response, array $args)
    {
        $id = (int) $args['id'];
        $this->task->deleteTask($id);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
} 