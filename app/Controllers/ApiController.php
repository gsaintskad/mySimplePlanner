<?php

namespace App\Controllers;

use App\Services\AuthService;
use Exception;

class ApiController
{
    protected $userId;
    protected $authService;

    public function __construct()
    {
        // Inicjalizujemy serwis autoryzacji, aby móc weryfikować tokeny
        $this->authService = new AuthService();
    }

    /**
     * Wymusza autentykację dla endpointu.
     * Logika HTTP (nagłówki) jest tutaj, logika weryfikacji tokena w Service.
     */
    protected function checkAuthentication()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (empty($authHeader) || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $this->sendJsonResponse(['error' => 'Authorization header missing or invalid'], 401);
            exit;
        }

        $token = $matches[1];

        try {
            // Delegujemy weryfikację do serwisu
            $this->userId = $this->authService->validateAccessToken($token);
            
        } catch (Exception $e) {
            // Błąd z serwisu (np. wygasły token)
            $this->sendJsonResponse(['error' => $e->getMessage()], 401);
            exit;
        }
    }

    /**
     * Pobiera dane wejściowe JSON z ciała żądania.
     */
    protected function getJsonInput(): ?array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    /**
     * Wysyła odpowiedź w formacie JSON.
     */
    protected function sendJsonResponse($data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}