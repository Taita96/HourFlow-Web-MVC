<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Models\User;

class LoginController
{

    public static function show(Router $router): void
    {
        $router->render('auth/Login', [
            'title' => 'Iniciar Sesión',
        ]);
    }

    public static function login(Router $router): void
    {
       verifyCsrf();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $router->render('auth/Login', [
                'title' => 'Iniciar Sesión',
                'error' => 'Credenciales inválidas'
            ]);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];

        header('Location: /');
        exit;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        session_destroy();
        header('Location: /login');
        exit;
    }
}
