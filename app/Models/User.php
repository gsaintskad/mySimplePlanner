<?php

namespace App\Models;

class User 
{
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Znajdź użytkownika po nazwie.
     * @param string $name
     * @return mixed (array z danymi użytkownika lub false)
     */
    public function findByName(string $name) 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Stwórz nowego użytkownika.
     * @param string $name
     * @param string $password (czyste hasło)
     * @return bool
     */
    public function create(string $name, string $password): bool
    {
        // Hashowanie hasła
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
        
        try {
            return $stmt->execute([$name, $hashedPassword]);
        } catch (\PDOException $e) {
            // Błąd (np użytkownik już istnieje)
            return false;
        }
    }

    /**
     * Znajdź użytkownika po refresh tokenie (i sprawdź, czy nie wygasł).
     * @param string $token
     * @return mixed
     */
    public function findByRefreshToken(string $token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE refresh_token = ? AND refresh_token_expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Zapisz refresh token dla użytkownika.
     * @param int $userId
     * @param string $token
     * @param string $expiryDate (w formacie Y-m-d H:i:s)
     * @return bool
     */
    public function saveRefreshToken(int $userId, string $token, string $expiryDate): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET refresh_token = ?, refresh_token_expires_at = ? WHERE id = ?");
        return $stmt->execute([$token, $expiryDate, $userId]);
    }

    /**
     * Wyczyść refresh token dla użytkownika (używane przy wylogowaniu).
     * @param int $userId
     * @return bool
     */
    public function clearRefreshToken(int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET refresh_token = NULL, refresh_token_expires_at = NULL WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}