<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Models\User;

class RegisterController
{
    public static function show(Router $router): void
    {
        $router->render('auth/Register', [
            'title' => 'Crear cuenta',
        ]);
    }

    public static function store(Router $router): void
    {
        verifyCsrf();
        
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errores = [];

        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido.';
        }

        if (strlen($password) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($password !== $passwordConfirm) {
            $errores[] = 'Las contraseñas no coinciden.';
        }

        if (empty($errores) && User::emailExists($email)) {
            $errores[] = 'Ya existe una cuenta con ese email.';
        }

        if (!empty($errores)) {
            $router->render('auth/Register', [
                'title' => 'Crear cuenta',
                'errores' => $errores,
                'nombre' => $nombre,
                'email' => $email,
            ]);
            return;
        }

        User::create($nombre, $email, $password);

        $user = User::findByEmail($email);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];

        header('Location: /');
        exit;
    }
}