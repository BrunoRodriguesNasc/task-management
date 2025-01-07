<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Controllers\TaskController;
use App\Models\Task;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Mockery;

class TaskControllerTest extends TestCase
{
    private $taskController;
    private $taskModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskModel = Mockery::mock(Task::class);
        $this->taskController = new TaskController($this->taskModel);
    }

    public function testGetTasks()
    {
        $expectedTasks = [
            [
                'id' => 1,
                'title' => 'Test Task',
                'status' => 'pending'
            ]
        ];

        $this->taskModel->shouldReceive('getAllTasks')
            ->once()
            ->andReturn($expectedTasks);

        $request = (new RequestFactory())->createRequest('GET', '/api/tasks');
        $response = (new ResponseFactory())->createResponse();

        $response = $this->taskController->getTasks($request, $response);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            json_encode($expectedTasks),
            (string) $response->getBody()
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 