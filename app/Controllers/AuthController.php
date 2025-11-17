<?php

namespace App\Controllers;

// -stare importy
use App\Services\AuthService; // new
use Exception; // Nowy import

class AuthController extends ApiController
{
    private $authService;

    public function __construct()
    {
        parent::__construct();
        // Kontroler tworzy instancję serwisu
        $this->authService = new AuthService();
    }

    /* 
    * Metody createAccessToken i createRefreshToken przeniesione do AuthService.
     */

    /*
     *
     * Ustawia refresh token jako bezpieczne ciasteczko HttpOnly.
     * Logika HTTP, zostaje w kontrolerze.
     */
    private function setRefreshTokenCookie(string $token)
    {
        setcookie('refreshToken', $token, [
            'expires' => time() + 604800, // 7 dni
            'path' => '/api/', // Dostępne tylko dla ścieżek API
            'httponly' => true, // Tylko HTTP (niewidoczne dla JavaScript)
            'secure' => false, // TODO: W produkcji ustaw na TRUE (wymaga HTTPS)
            'samesite' => 'Strict' // Ochrona CSRF
        ]);
    }

    /**
     * Czyści ciasteczko refresh tokena.
     * zostaje w kontrolerze
     */
    private function clearRefreshTokenCookie()
    {
        setcookie('refreshToken', '', [
            'expires' => time() - 3600, // Ustaw przeszłą datę
            'path' => '/api/',
            'httponly' => true,
            'secure' => false, // TODO: W produkcji TRUE
            'samesite' => 'Strict'
        ]);
    }


    /**
     * Obsługuje żądanie logowania.
     */
    public function handleLogin()
    {
        try {
            // 1. Pobierz dane HTTP
            $data = $this->getJsonInput();
            $name = $data['name'] ?? '';
            $password = $data['password'] ?? '';

            // 2. Wywołaj logikę biznesową (Serwis)
            $tokens = $this->authService->login($name, $password);

            // 3. Obsłuż odpowiedź HTTP
            $this->setRefreshTokenCookie($tokens['refreshToken']);
            $this->sendJsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'token' => $tokens['accessToken']
            ], 200);

        } catch (Exception $e) {
            // 4. Obsłuż błędy z serwisu
            $this->sendJsonResponse(
                ['success' => false, 'message' => $e->getMessage()], 
                $e->getCode() ?: 401 // Użyj kodu błędu z wyjątku (domyślnie 401)
            );
        }
    }

    /**
     * Obsługuje żądanie odświeżenia tokena.
     */
    public function handleRefresh()
    {
        try {
            // 1. Pobierz dane HTTP
            $refreshToken = $_COOKIE['refreshToken'] ?? '';
            
            // 2. Wywołaj logikę biznesową (Serwis)
            $newAccessToken = $this->authService->refresh($refreshToken);

            // 3. Obsłuż odpowiedź HTTP
            $this->sendJsonResponse([
                'success' => true,
                'token' => $newAccessToken
            ], 200);

        } catch (Exception $e) {
            // 4. Obsłuż błędy
            $this->sendJsonResponse(
                ['success' => false, 'message' => $e->getMessage()], 
                $e->getCode() ?: 401
            );
        }
    }

    /**
     * Obsługuje żądanie wylogowania.
     */
    public function handleLogout()
    {
        try {
            // 1. Pobierz dane HTTP
            $refreshToken = $_COOKIE['refreshToken'] ?? '';
            
            // 2. Wywołaj logikę biznesową (Serwis)
            $this->authService->logout($refreshToken);

            // 3. Obsłuż odpowiedź HTTP
            $this->clearRefreshTokenCookie();
            $this->sendJsonResponse(['success' => true, 'message' => 'Logged out'], 200);

        } catch (Exception $e) {
            // 4. Obsłuż błędy (chociaż logout nie powinien rzucać błędów)
            $this->sendJsonResponse(
                ['success' => false, 'message' => $e->getMessage()], 
                $e->getCode() ?: 500
            );
        }
    }
    
    /**
     * Obsługuje żądanie rejestracji.
     */
    public function handleRegistration()
    {
        try {
            // 1. Pobierz dane HTTP
            $data = $this->getJsonInput();
            $name = $data['name'] ?? '';
            $password = $data['password'] ?? '';

            // 2. Wywołaj logikę biznesową (Serwis)
            $this->authService->register($name, $password);

            // 3. Obsłuż odpowiedź HTTP
            $this->sendJsonResponse(['success' => true, 'message' => 'Registration successful'], 201);

        } catch (Exception $e) {
            // 4. Obsłuż błędy
            $this->sendJsonResponse(
                ['success' => false, 'message' => $e->getMessage()], 
                $e->getCode() ?: 400 // Domyślnie 400 dla złych danych
            );
        }
    }
}