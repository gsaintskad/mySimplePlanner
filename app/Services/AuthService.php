<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Exception;

/**
 * cała logika biznesowa związana z uwierzytelnianiem użytkowników.
 */
class AuthService
{
    private $userModel;
    private $jwtSecret;

    public function __construct()
    {
        // Serwis sam zarządza swoimi zależnościami (modelem)
        $this->userModel = new User();
        
        $this->jwtSecret = getenv('JWT_SECRET');
        if (!$this->jwtSecret) {
            // Błąd konfiguracji
            throw new Exception("JWT_SECRET environment variable is not set.", 500);
        }
    }

    private function createAccessToken(int $userId, string $userName): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 900; // 15 minut
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => ['userId' => $userId, 'userName' => $userName]
        ];
        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    private function createRefreshToken(int $userId): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 604800; // 7 dni
        $expiryDateDb = date('Y-m-d H:i:s', $expirationTime);
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => ['userId' => $userId]
        ];
        
        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');
        
        // Zapisz token w bazie danych
        $this->userModel->saveRefreshToken($userId, $token, $expiryDateDb);
        
        return $token;
    }

    /**
     * rejestracja
     */

    public function register(string $name, string $password): void
    {
        if (empty($name) || empty($password)) {
            // Kod 400 - Bad Request
            throw new Exception('Name and password are required', 400);
        }

        if ($this->userModel->findByName($name)) {
            // Kod 409 - Conflict
            throw new Exception('User with this name already exists', 409);
        }

        $success = $this->userModel->create($name, $password);
        if (!$success) {
            // Kod 500 - Internal Server Error
            throw new Exception('Error during registration', 500);
        }
    }

    /**
     * logowanie
     */
    public function login(string $name, string $password): array
    {
        $user = $this->userModel->findByName($name);

        if (!$user || !password_verify($password, $user['password'])) {
            // Kod 401 - Unauthorized
            throw new Exception('Invalid name or password', 401);
        }

        $accessToken = $this->createAccessToken($user['id'], $user['name']);
        $refreshToken = $this->createRefreshToken($user['id']);

        return [
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        ];
    }

    /**
     * refresh
     */
    public function refresh(string $refreshToken): string
    {
        if (empty($refreshToken)) {
            throw new Exception('Refresh token not found', 401);
        }

        $user = $this->userModel->findByRefreshToken($refreshToken);
        
        if (!$user) {
            throw new Exception('Invalid or expired refresh token', 401);
        }

        // Token jest ważny, wygeneruj nowy access token
        return $this->createAccessToken($user['id'], $user['name']);
    }

    /**
     * logout
     */
    public function logout(string $refreshToken): void
    {
        if (empty($refreshToken)) {
            // Brak tokena
            return;
        }
        
        $user = $this->userModel->findByRefreshToken($refreshToken);
        if ($user) {
            // Wyczyść token w bazie
            $this->userModel->clearRefreshToken($user['id']);
        }
        // Jeśli użytkownik nie istnieje, token i tak jest nieważny
    }
}