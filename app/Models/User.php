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
}