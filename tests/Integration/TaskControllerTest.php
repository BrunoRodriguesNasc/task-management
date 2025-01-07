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

    public function testCreateTask()
    {
        $taskData = [
            'title' => 'New Task',
            'status' => 'pending',
            'user_id' => 1
        ];

        $this->taskModel->shouldReceive('createTask')
            ->with($taskData)
            ->once()
            ->andReturn(['id' => 1] + $taskData);

        $request = (new RequestFactory())->createRequest('POST', '/api/tasks')
            ->withParsedBody($taskData);
        $response = (new ResponseFactory())->createResponse();

        $response = $this->taskController->createTask($request, $response);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
    }

    public function testUpdateTask()
    {
        $taskId = 1;
        $taskData = [
            'title' => 'Updated Task',
            'status' => 'completed'
        ];

        $this->taskModel->shouldReceive('updateTask')
            ->with($taskId, $taskData)
            ->once()
            ->andReturn(['id' => $taskId] + $taskData);

        $request = (new RequestFactory())->createRequest('PUT', "/api/tasks/{$taskId}")
            ->withParsedBody($taskData);
        $response = (new ResponseFactory())->createResponse();

        $response = $this->taskController->updateTask($request, $response, ['id' => $taskId]);
        
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertTrue($responseData['success']);
    }

    public function testDeleteTask()
    {
        $taskId = 1;

        $this->taskModel->shouldReceive('deleteTask')
            ->with($taskId)
            ->once()
            ->andReturn(true);

        $request = (new RequestFactory())->createRequest('DELETE', "/api/tasks/{$taskId}");
        $response = (new ResponseFactory())->createResponse();

        $response = $this->taskController->deleteTask($request, $response, ['id' => $taskId]);
        
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertTrue($responseData['success']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 