<?php

namespace App\Controllers;

use App\Services\TaskService; // Importujemy nowy serwis
use Exception;

class TaskController extends ApiController
{
    private $taskService;

    public function __construct()
    {
        parent::__construct();
        // Wszystkie akcje w tym kontrolerze wymagają zalogowanego użytkownika
        // To ustawi $this->userId (z ApiController)
        $this->checkAuthentication(); 
        
        // Inicjalizacja serwisu
        $this->taskService = new TaskService();
    }

    // GET /api/tasks
    public function listTasks()
    {
        try {
            $tasks = $this->taskService->getUserTasks($this->userId);
            $this->sendJsonResponse($tasks, 200);
        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    // GET /api/tasks/{id}
    public function getTask(int $taskId)
    {
        try {
            $task = $this->taskService->getTask($taskId, $this->userId);
            $this->sendJsonResponse($task, 200);
        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 404);
        }
    }

    // POST /api/tasks
    public function create()
    {
        try {
            $data = $this->getJsonInput();
            $newTask = $this->taskService->createTask($this->userId, $data);
            $this->sendJsonResponse($newTask, 201); // 201 Created
        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    // PUT /api/tasks/{id}
    public function update(int $taskId)
    {
        try {
            $data = $this->getJsonInput();
            $this->taskService->updateTask($taskId, $this->userId, $data);
            $this->sendJsonResponse(['success' => true, 'message' => 'Task updated'], 200);
        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    // DELETE /api/tasks/{id}
    public function delete(int $taskId)
    {
        try {
            $this->taskService->deleteTask($taskId, $this->userId);
            $this->sendJsonResponse(null, 204); // 204 No Content
        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}