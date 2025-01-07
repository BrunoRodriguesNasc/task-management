<?php

namespace App\Models;

use PDO;
use InvalidArgumentException;

class Task
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllTasks()
    {
        $sql = "SELECT t.*, u.name as user_name 
                FROM tasks t 
                LEFT JOIN users u ON t.user_id = u.id";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask($data)
    {
        $this->validateTaskData($data);

        $sql = "INSERT INTO tasks (title, description, due_date, status, user_id) 
                VALUES (:title, :description, :due_date, :status, :user_id) 
                RETURNING *";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'user_id' => $data['user_id'] ?? null
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    private function validateTaskData($data)
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Title is required');
        }

        if (!empty($data['status']) && 
            !in_array($data['status'], ['pending', 'in_progress', 'completed'])) {
            throw new InvalidArgumentException('Invalid status value');
        }

        if (!empty($data['user_id'])) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$data['user_id']]);
            if (!$stmt->fetch()) {
                throw new InvalidArgumentException('Invalid user ID');
            }
        } else {
            throw new InvalidArgumentException('User ID is required');
        }

        if (!empty($data['due_date']) && !strtotime($data['due_date'])) {
            throw new InvalidArgumentException('Invalid date format');
        }
    }

    public function updateTask($id, $data)
    {
        $this->validateTaskData($data);

        $existingTask = $this->getTaskById($id);
        if (!$existingTask) {
            throw new InvalidArgumentException('Task not found');
        }

        $updates = [];
        $params = [];

        // Construir atualização dinâmica
        foreach (['title', 'description', 'due_date', 'status', 'user_id'] as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($updates)) {
            return $existingTask;
        }

        $params['id'] = $id;
        $sql = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = :id RETURNING *";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteTask($id)
    {
        $existingTask = $this->getTaskById($id);
        if (!$existingTask) {
            throw new InvalidArgumentException('Task not found');
        }

        $sql = "DELETE FROM tasks WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute(['id' => $id]);
    }

    private function getTaskById($id)
    {
        $sql = "SELECT * FROM tasks WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
} 