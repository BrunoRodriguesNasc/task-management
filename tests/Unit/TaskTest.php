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

        $pdoStatement = Mockery::mock(\PDOStatement::class);
        $pdoStatement->shouldReceive('execute')
            ->with(Mockery::subset($taskData))
            ->andReturn(true);

        $pdoStatement->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->andReturn(['id' => 1] + $taskData);

        $this->dbMock->shouldReceive('prepare')
            ->andReturn($pdoStatement);

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
            'status' => 'invalid_status'
        ];

        $this->taskModel->createTask($taskData);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 