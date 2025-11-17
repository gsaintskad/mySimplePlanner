<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ApiController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    // Generuje Access Token (krótkożyjący)
    private function createAccessToken(int $userId, string $userName): string
    {
        $secretKey = getenv('JWT_SECRET');
        $issuedAt = time();
        $expirationTime = $issuedAt + 900; // Ważny 15 minut (900 sekund)
        
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => [
                'userId' => $userId,
                'userName' => $userName
            ]
        ];
        return JWT::encode($payload, $secretKey, 'HS256');
    }

    // Generuje Refresh Token (długożyjący) i zapisuje w DB
    private function createRefreshToken(int $userId): string
    {
        $secretKey = getenv('JWT_SECRET');
        $issuedAt = time();
        $expirationTime = $issuedAt + 604800; // Ważny 7 dni (604800 sekund)
        $expiryDateDb = date('Y-m-d H:i:s', $expirationTime);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => [
                'userId' => $userId
            ]
        ];
        
        $token = JWT::encode($payload, $secretKey, 'HS256');

        // Zapisz token w bazie danych
        $this->userModel->saveRefreshToken($userId, $token, $expiryDateDb);
        
        return $token;
    }

    // Ustawia refresh token jako bezpieczne ciasteczko HttpOnly
    private function setRefreshTokenCookie(string $token)
    {
        setcookie('refreshToken', $token, [
            'expires' => time() + 604800, // 7 dni
            'path' => '/api/', // Dostępne tylko dla ścieżek API
            'httponly' => true, // Tylko HTTP (niewidoczne dla JavaScript)
            'secure' => false, // TODO: W produkcji bedzie TRUE (wymaga HTTPS)
            'samesite' => 'Strict' // Ochrona CSRF
        ]);
    }

    // Przetwarza dane logowania
    public function handleLogin()
    {
        $data = $this->getJsonInput();
        $name = $data['name'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByName($name);

        if ($user && password_verify($password, $user['password'])) {
            // Hasło poprawne - generuj tokeny
            $accessToken = $this->createAccessToken($user['id'], $user['name']);
            $refreshToken = $this->createRefreshToken($user['id']);
            
            // Ustaw refresh token w cookie
            $this->setRefreshTokenCookie($refreshToken);

            // Zwróć TYLKO access token klientowi w JSON
            $this->sendJsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'token' => $accessToken // Zwracamy tylko access token
            ], 200);
            
        } else {
            $this->sendJsonResponse(['success' => false, 'message' => 'Invalid name or password'], 401);
        }
    }

    // Nowa funkcja do odświeżania tokena
    public function handleRefresh()
    {
        $refreshToken = $_COOKIE['refreshToken'] ?? '';

        if (empty($refreshToken)) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Refresh token not found'], 401);
            return;
        }

        try {
            // Sprawdź, czy token jest w bazie i jest ważny
            $user = $this->userModel->findByRefreshToken($refreshToken);
            
            if (!$user) {
                throw new \Exception('Invalid or expired refresh token');
            }

            // Token jest ważny, wygeneruj nowy access token
            $accessToken = $this->createAccessToken($user['id'], $user['name']);

            $this->sendJsonResponse([
                'success' => true,
                'token' => $accessToken
            ], 200);

        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Unauthorized: ' . $e->getMessage()], 401);
        }
    }

    // Nowa funkcja wylogowywania
    public function handleLogout()
    {
        $refreshToken = $_COOKIE['refreshToken'] ?? '';
        
        if (!empty($refreshToken)) {
            // Spróbuj znaleźć użytkownika po tokenie
            $user = $this->userModel->findByRefreshToken($refreshToken);
            if ($user) {
                // Wyczyść token w bazie
                $this->userModel->clearRefreshToken($user['id']);
            }
        }
        
        // Wyczyść ciasteczko po stronie klienta
        setcookie('refreshToken', '', [
            'expires' => time() - 3600, // Ustaw przeszłą datę
            'path' => '/api/',
            'httponly' => true,
            'secure' => false, // TODO: W produkcji TRUE
            'samesite' => 'Strict'
        ]);

        $this->sendJsonResponse(['success' => true, 'message' => 'Logged out'], 200);
    }

    // Funkcja rejestracji (bez zmian)
    public function handleRegistration()
    {
        $data = $this->getJsonInput();
        $name = $data['name'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($name) || empty($password)) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Name and password are required'], 400);
            return;
        }

        $userModel = new User();

        if ($userModel->findByName($name)) {
            $this->sendJsonResponse(['success' => false, 'message' => 'User with this name already exists'], 409); // 409 Conflict
            return;
        }

        $success = $userModel->create($name, $password);

        if ($success) {
            $this->sendJsonResponse(['success' => true, 'message' => 'Registration successful'], 201); // 201 Created
        } else {
            $this->sendJsonResponse(['success' => false, 'message' => 'Error during registration'], 500);
        }
    }
}