<?php

namespace App\Controllers;

class PageController 
{
    // Wyświetla główną stronę po zalogowaniu
    public function main() 
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit;
        }

        $username = $_SESSION['user'];
        
        // Przekaż zmienną $username do widoku
        $this->renderView('main', ['username' => $username]);
    }

    // Funkcja pomocnicza do renderowania widoku
    protected function renderView(string $viewName, array $data = [])
    {
        // Udostępnij zmienne z tablicy $data w widoku
        extract($data);

        require_once __DIR__ . "/../Views/{$viewName}.php";
    }
}