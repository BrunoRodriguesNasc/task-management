<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use PDO;
use Mockery;

class TaskTest extends TestCase
{
    private $taskModel;
    private $dbMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = Mockery::mock(PDO::class);
        $this->taskModel = new Task($this->dbMock);
    }

    public function testGetAllTasks()
    {
        $pdoStatement = Mockery::mock(\PDOStatement::class);
        $expectedTasks = [
            [
                'id' => 1,
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending',
                'user_name' => 'John Doe'
            ]
        ];

        $pdoStatement->shouldReceive('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->andReturn($expectedTasks);

        $this->dbMock->shouldReceive('query')
            ->andReturn($pdoStatement);

        $result = $this->taskModel->getAllTasks();
        $this->assertEquals($expectedTasks, $result);
    }

    public function testCreateTask()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'due_date' => '2024-01-15',
            'status' => 'pending',
            'user_id' => 1
        ];

        // Mock para verificação de usuário
        $userCheckStmt = Mockery::mock(\PDOStatement::class);
        $userCheckStmt->shouldReceive('execute')
            ->with([$taskData['user_id']])
            ->once()
            ->andReturn(true);
        $userCheckStmt->shouldReceive('fetch')
            ->once()
            ->andReturn(['id' => 1]);

        // Mock para criação da tarefa
        $createTaskStmt = Mockery::mock(\PDOStatement::class);
        $createTaskStmt->shouldReceive('execute')
            ->withArgs(function ($args) use ($taskData) {
                return $args['title'] === $taskData['title'] &&
                       $args['description'] === $taskData['description'] &&
                       $args['due_date'] === $taskData['due_date'] &&
                       $args['status'] === $taskData['status'] &&
                       $args['user_id'] === $taskData['user_id'];
            })
            ->once()
            ->andReturn(true);
        $createTaskStmt->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->once()
            ->andReturn(['id' => 1] + $taskData);

        $this->dbMock->shouldReceive('prepare')
            ->andReturn($userCheckStmt, $createTaskStmt);

        $result = $this->taskModel->createTask($taskData);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(1, $result['id']);
    }

    public function testValidateTaskDataWithInvalidStatus()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status value');

        $taskData = [
            'title' => 'Test Task',
            'status' => 'invalid_status',
            'user_id' => 1,
            'description' => 'Test Description',
            'due_date' => '2024-01-15'
        ];

        $this->taskModel->createTask($taskData);
    }

    public function testUpdateTask()
    {
        $taskId = 1;
        $taskData = [
            'title' => 'Updated Task',
            'status' => 'completed',
            'user_id' => 1
        ];

        // Mock para verificação de usuário
        $userCheckStmt = Mockery::mock(\PDOStatement::class);
        $userCheckStmt->shouldReceive('execute')
            ->with([$taskData['user_id']])
            ->once()
            ->andReturn(true);
        $userCheckStmt->shouldReceive('fetch')
            ->once()
            ->andReturn(['id' => 1]);

        // Mock para getTaskById
        $getTaskStmt = Mockery::mock(\PDOStatement::class);
        $getTaskStmt->shouldReceive('execute')
            ->with(['id' => $taskId])
            ->once()
            ->andReturn(true);
        $getTaskStmt->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->once()
            ->andReturn(['id' => $taskId, 'title' => 'Old Title']);

        // Mock para updateTask
        $updateTaskStmt = Mockery::mock(\PDOStatement::class);
        $updateTaskStmt->shouldReceive('execute')
            ->withArgs(function ($args) use ($taskId, $taskData) {
                return isset($args['id']) && $args['id'] === $taskId &&
                       isset($args['title']) && $args['title'] === $taskData['title'] &&
                       isset($args['status']) && $args['status'] === $taskData['status'];
            })
            ->once()
            ->andReturn(true);
        $updateTaskStmt->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->once()
            ->andReturn(['id' => $taskId] + $taskData);

        $this->dbMock->shouldReceive('prepare')
            ->andReturn($userCheckStmt, $getTaskStmt, $updateTaskStmt);

        $result = $this->taskModel->updateTask($taskId, $taskData);
        $this->assertEquals($taskId, $result['id']);
        $this->assertEquals('Updated Task', $result['title']);
    }

    public function testDeleteTask()
    {
        $taskId = 1;

        // Mock para getTaskById
        $getTaskStmt = Mockery::mock(\PDOStatement::class);
        $getTaskStmt->shouldReceive('execute')
            ->with(['id' => $taskId])
            ->once()
            ->andReturn(true);
        $getTaskStmt->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->once()
            ->andReturn(['id' => $taskId]);

        // Mock para deleteTask
        $deleteTaskStmt = Mockery::mock(\PDOStatement::class);
        $deleteTaskStmt->shouldReceive('execute')
            ->with(['id' => $taskId])
            ->once()
            ->andReturn(true);

        $this->dbMock->shouldReceive('prepare')
            ->andReturn($getTaskStmt, $deleteTaskStmt);

        $result = $this->taskModel->deleteTask($taskId);
        $this->assertTrue($result);
    }

    public function testCreateTaskWithInvalidDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date format');

        $taskData = [
            'title' => 'Test Task',
            'status' => 'pending',
            'user_id' => 1,
            'due_date' => 'invalid-date'
        ];

        // Mock para verificação de usuário
        $pdoStatement = Mockery::mock(\PDOStatement::class);
        $pdoStatement->shouldReceive('execute')
            ->with([$taskData['user_id']])
            ->andReturn(true);
        $pdoStatement->shouldReceive('fetch')
            ->andReturn(['id' => 1]); // Simula que o usuário existe

        $this->dbMock->shouldReceive('prepare')
            ->with(Mockery::any())
            ->andReturn($pdoStatement);

        $this->taskModel->createTask($taskData);
    }

    public function testCreateTaskWithInvalidUserId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid user ID');

        $taskData = [
            'title' => 'Test Task',
            'status' => 'pending',
            'user_id' => 999
        ];

        $pdoStatement = Mockery::mock(\PDOStatement::class);
        $pdoStatement->shouldReceive('execute')
            ->with([999])
            ->once();
        $pdoStatement->shouldReceive('fetch')
            ->andReturn(false);

        $this->dbMock->shouldReceive('prepare')
            ->andReturn($pdoStatement);

        $this->taskModel->createTask($taskData);
    }

    public function testUpdateTaskNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task not found');

        $taskId = 999;
        $taskData = [
            'title' => 'Updated Task',
            'user_id' => 1
        ];

        $userCheckStatement = Mockery::mock(\PDOStatement::class);
        $userCheckStatement->shouldReceive('execute')
            ->with([$taskData['user_id']])
            ->andReturn(true);
        $userCheckStatement->shouldReceive('fetch')
            ->andReturn(['id' => 1]); // Usuário existe

        // Mock para getTaskById
        $taskCheckStatement = Mockery::mock(\PDOStatement::class);
        $taskCheckStatement->shouldReceive('execute')
            ->with(['id' => $taskId])
            ->andReturn(true);
        $taskCheckStatement->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->andReturn(false); // Tarefa não existe

        $this->dbMock->shouldReceive('prepare')
            ->andReturn($userCheckStatement, $taskCheckStatement);

        $this->taskModel->updateTask($taskId, $taskData);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 