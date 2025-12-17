<?php

namespace App\Services;

use App\Models\Task;
use Exception;

class TaskService
{
    private $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    /**
     * Pobiera wszystkie zadania użytkownika.
     */
    public function getUserTasks(int $userId): array
    {
        return $this->taskModel->findByUserId($userId);
    }

    /**
     * Pobiera jedno zadanie, sprawdzając uprawnienia.
     */
    public function getTask(int $taskId, int $userId)
    {
        // Sprawdź czy zadanie istnieje i należy do usera
        if (!$this->taskModel->isOwner($taskId, $userId)) {
            throw new Exception('Task not found or permission denied', 404);
        }

        return $this->taskModel->findById($taskId);
    }

    /**
     * Tworzy nowe zadanie.
     */
    public function createTask(int $userId, array $data)
    {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $priority = (int) ($data['priority'] ?? 1);
        $dueDate = $data['due_date'] ?? '';

        // Walidacja
        if (empty($title) || empty($dueDate)) {
            throw new Exception('Title and due_date are required', 400);
        }

        $newTaskId = $this->taskModel->create($userId, $title, $description, $priority, $dueDate);
        
        if (!$newTaskId) {
            throw new Exception('Failed to create task', 500);
        }

        // Zwracamy nowo utworzony obiekt
        return $this->taskModel->findById($newTaskId);
    }

    /**
     * Aktualizuje zadanie.
     */
    public function updateTask(int $taskId, int $userId, array $data): void
    {
        // 1. Sprawdź uprawnienia
        if (!$this->taskModel->isOwner($taskId, $userId)) {
            throw new Exception('Task not found or permission denied', 404);
        }

        // 2. Walidacja danych
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $priority = (int) ($data['priority'] ?? 1);
        $dueDate = $data['due_date'] ?? '';

        if (empty($title) || empty($dueDate)) {
            throw new Exception('Title and due_date are required', 400);
        }

        // 3. Aktualizacja
        $success = $this->taskModel->update($taskId, $title, $description, $priority, $dueDate);
        
        if (!$success) {
            throw new Exception('Failed to update task', 500);
        }
    }

    /**
     * Usuwa zadanie.
     */
    public function deleteTask(int $taskId, int $userId): void
    {
        if (!$this->taskModel->isOwner($taskId, $userId)) {
            throw new Exception('Task not found or permission denied', 404);
        }

        $success = $this->taskModel->delete($taskId);

        if (!$success) {
            throw new Exception('Failed to delete task', 500);
        }
    }
}