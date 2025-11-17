<?php

namespace App\Controllers;

use App\Models\Task;

class TaskController extends ApiController
{
    private $taskModel;

    public function __construct()
    {
        parent::__construct();
        // Wszystkie akcje w tym kontrolerze wymagają zalogowanego użytkownika
        $this->checkAuthentication(); 
        $this->taskModel = new Task();
    }

    // Pobiera wszystkie zadania dla zalogowanego użytkownika
    // Metoda: GET, Endpoint: /api/tasks
    public function listTasks()
    {
        $tasks = $this->taskModel->findByUserId($this->userId);
        $this->sendJsonResponse($tasks, 200);
    }

    // Pobiera jedno zadanie
    // Metoda: GET, Endpoint: /api/tasks/{id}
    public function getTask(int $taskId)
    {
        if (!$this->taskModel->isOwner($taskId, $this->userId)) {
            $this->sendJsonResponse(['error' => 'Task not found or permission denied'], 404);
            return;
        }
        $task = $this->taskModel->findById($taskId);
        $this->sendJsonResponse($task, 200);
    }


    // Tworzenie nowego zadania
    // Metoda: POST, Endpoint: /api/tasks
    public function create()
    {
        $data = $this->getJsonInput();
        
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $priority = (int) ($data['priority'] ?? 1);
        $dueDate = $data['due_date'] ?? '';

        if (empty($title) || empty($dueDate)) {
            $this->sendJsonResponse(['error' => 'Title and due_date are required'], 400);
            return;
        }

        // Metoda create zwróci teraz ID nowego zadania lub false
        $newTaskId = $this->taskModel->create($this->userId, $title, $description, $priority, $dueDate);
        
        if ($newTaskId) {
            // Pobierz nowo utworzony obiekt zadania na podstawie jego ID
            $newTask = $this->taskModel->findById($newTaskId);
            
            // Zwróć pełny obiekt zadania i status 201 (Created)
            $this->sendJsonResponse($newTask, 201);
        } else {
            $this->sendJsonResponse(['error' => 'Failed to create task'], 500);
        }
    }


    // Aktualizuje zadanie
    // Metoda: PUT, Endpoint: /api/tasks/{id}
    public function update(int $taskId)
    {
        $data = $this->getJsonInput();
        
        if (!$this->taskModel->isOwner($taskId, $this->userId)) {
            $this->sendJsonResponse(['error' => 'Task not found or permission denied'], 404);
            return;
        }
        
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $priority = (int) ($data['priority'] ?? 1);
        $dueDate = $data['due_date'] ?? '';

        if (empty($title) || empty($dueDate)) {
            $this->sendJsonResponse(['error' => 'Title and due_date are required'], 400);
            return;
        }

        $success = $this->taskModel->update($taskId, $title, $description, $priority, $dueDate);
        
        if ($success) {
            $this->sendJsonResponse(['success' => true, 'message' => 'Task updated'], 200);
        } else {
            $this->sendJsonResponse(['error' => 'Failed to update task'], 500);
        }
    }

    // Usuwa zadanie
    // Metoda: DELETE, Endpoint: /api/tasks/{id}
    public function delete(int $taskId)
    {
        if (!$this->taskModel->isOwner($taskId, $this->userId)) {
            $this->sendJsonResponse(['error' => 'Task not found or permission denied'], 404);
            return;
        }

        $success = $this->taskModel->delete($taskId);

        if ($success) {
            $this->sendJsonResponse(null, 204); // 204 No Content
        } else {
            $this->sendJsonResponse(['error' => 'Failed to delete task'], 500);
        }
    }
}