<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key; // Dodano import Key
use Exception;

class AuthService
{
    private $userModel;
    private $jwtSecret;

    public function __construct()
    {
        $this->userModel = new User();
        $this->jwtSecret = getenv('JWT_SECRET');
        if (!$this->jwtSecret) {
            throw new Exception("JWT_SECRET environment variable is not set.", 500);
        }
    }

    /* ... metody login, register, refresh, logout (bez zmian) ... */

    // --- NOWA METODA ---
    /**
     * Weryfikuje access token i zwraca userId.
     */
    public function validateAccessToken(string $token): int
    {
        try {
            // Dekodowanie tokena (biblioteka rzuci wyjątek, jeśli token jest nieprawidłowy lub wygasł)
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            
            // Zwróć ID użytkownika z payloadu
            return $decoded->data->userId;
        } catch (Exception $e) {
            // Przechwytujemy wyjątek biblioteki i rzucamy własny z odpowiednim kodem
            throw new Exception('Unauthorized: ' . $e->getMessage(), 401);
        }
    }
    
    // ... reszta metod prywatnych (createAccessToken itp.) ...
    
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
        $this->userModel->saveRefreshToken($userId, $token, $expiryDateDb);
        return $token;
    }

    public function register(string $name, string $password): void
    {
        if (empty($name) || empty($password)) throw new Exception('Name and password are required', 400);
        if ($this->userModel->findByName($name)) throw new Exception('User with this name already exists', 409);
        if (!$this->userModel->create($name, $password)) throw new Exception('Error during registration', 500);
    }

    public function login(string $name, string $password): array
    {
        $user = $this->userModel->findByName($name);
        if (!$user || !password_verify($password, $user['password'])) throw new Exception('Invalid name or password', 401);

        return [
            'accessToken' => $this->createAccessToken($user['id'], $user['name']),
            'refreshToken' => $this->createRefreshToken($user['id'])
        ];
    }

    public function refresh(string $refreshToken): string
    {
        if (empty($refreshToken)) throw new Exception('Refresh token not found', 401);
        $user = $this->userModel->findByRefreshToken($refreshToken);
        if (!$user) throw new Exception('Invalid or expired refresh token', 401);
        return $this->createAccessToken($user['id'], $user['name']);
    }

    public function logout(string $refreshToken): void
    {
        if (!empty($refreshToken)) {
            $user = $this->userModel->findByRefreshToken($refreshToken);
            if ($user) $this->userModel->clearRefreshToken($user['id']);
        }
    }
}