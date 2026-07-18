<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Models\Tienda;

class TiendaController
{
    public static function index(Router $router): void
    {
        requireAuth();

        $tiendas = Tienda::allByUser((int) $_SESSION['user_id']);

        $router->render('tiendas/Index', [
            'title' => 'Empresas y tiendas',
            'tiendas' => $tiendas,
        ]);
    }

    public static function create(Router $router): void
    {
        requireAuth();

        $router->render('tiendas/Create', [
            'title' => 'Nueva tienda',
        ]);
    }

    public static function store(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        $name = trim($_POST['nombre'] ?? '');

        if ($name === '') {
            $router->render('tiendas/Create', [
                'title' => 'Nueva tienda',
                'error' => 'El nombre es obligatorio.',
                'nombre' => $name,
            ]);
            return;
        }

        Tienda::create((int) $_SESSION['user_id'], $name);

        header('Location: /tiendas');
        exit;
    }

    public static function edit(Router $router): void
    {
        requireAuth();

        $tienda = Tienda::find((int) ($_GET['id'] ?? 0), (int) $_SESSION['user_id']);

        if (!$tienda) {
            header('Location: /tiendas');
            exit;
        }

        $router->render('tiendas/Edit', [
            'title' => 'Editar tienda',
            'tienda' => $tienda,
        ]);
    }

    public static function update(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $userId = (int) $_SESSION['user_id'];
        $tienda = Tienda::find($id, $userId);

        if (!$tienda) {
            header('Location: /tiendas');
            exit;
        }

        $name = trim($_POST['nombre'] ?? '');

        if ($name === '') {
            $router->render('tiendas/Edit', [
                'title' => 'Editar tienda',
                'tienda' => $tienda,
                'error' => 'El nombre es obligatorio.',
            ]);
            return;
        }

        Tienda::update($id, $userId, $name);

        header('Location: /tiendas');
        exit;
    }

    public static function destroy(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        Tienda::delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);

        header('Location: /tiendas');
        exit;
    }
}