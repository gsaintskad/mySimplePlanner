<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends PageController
{
    // Wyświetla formularz logowania.
    public function showLoginForm(string $message = '') 
    {
        $this->renderView('login', ['message' => $message]);
    }

    // Przetwarza dane logowania z formularza POST

    public function handleLogin() 
    {
        $name = $_POST['name'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $userModel = new User();
        $user = $userModel->findByName($name);

        // Czy użytkownik istnieje i czy hasło jest poprawne?
        if ($user && password_verify($password, $user['password'])) {
            // Hasło poprawne!
            $_SESSION['user'] = $user['name'];
            header("Location: /main");
            exit;
        } else {
            // Błędna nazwa lub hasło
            $this->showLoginForm('Nieprawidłowa nazwa użytkownika lub hasło.');
        }
    }


    // Wylogowywanie użytkownika.

    public function logout() 
    {
        session_destroy();
        header("Location: /");
        exit;
    }

    // REJESTRACJA

    // Wyświetla formularz rejestracji.
    public function showRegistrationForm(string $message = '')
    {
        $this->renderView('register', ['message' => $message]);
    }

    // Przetwarza dane rejestracji z formularza POST
    public function handleRegistration()
    {
        $name = $_POST['name'] ?? '';
        $password = $_POST['password'] ?? '';

        // Prosta walidacja
        if (empty($name) || empty($password)) {
            $this->showRegistrationForm('Nazwa użytkownika i hasło są wymagane.');
            return;
        }
        
        $userModel = new User();
        
        // Czy użytkownik już istnieje?
        if ($userModel->findByName($name)) {
            $this->showRegistrationForm('Użytkownik o tej nazwie już istnieje.');
            return;
        }

        // Utwórz użytkownika
        $success = $userModel->create($name, $password);

        if ($success) {
            // Przekieruj do logowania po udanej rejestracji
            header("Location: /");
            exit;
        } else {
            $this->showRegistrationForm('Wystąpił błąd podczas rejestracji. Spróbuj ponownie.');
        }
    }
}