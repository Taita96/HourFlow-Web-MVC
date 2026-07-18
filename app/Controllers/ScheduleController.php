<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Models\Schedule;
use App\Models\Tienda;
use App\Services\OvertimeCalculator;

class ScheduleController
{
    public static function index(Router $router): void
    {
        requireAuth();

        $schedules = Schedule::allByUser((int) $_SESSION['user_id']);

        $router->render('schedules/Index', [
            'title' => 'Mis horarios',
            'schedules' => $schedules,
        ]);
    }

    public static function create(Router $router): void
    {
        requireAuth();

        $router->render('schedules/Create', [
            'title' => 'Nuevo horario',
            'tiendas' => Tienda::allByUser((int) $_SESSION['user_id']),
        ]);
    }

    public static function store(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        $userId = (int) $_SESSION['user_id'];
        $name = trim($_POST['nombre'] ?? '');
        $tiendaId = (int) ($_POST['tienda_id'] ?? 0);

        [$errors, $blocks] = self::validate($name, $_POST['hora_inicio'] ?? [], $_POST['hora_fin'] ?? []);

        if ($tiendaId <= 0 || !Tienda::find($tiendaId, $userId)) {
            $errors[] = 'Selecciona una tienda válida.';
        }

        if (!empty($errors)) {
            $router->render('schedules/Create', [
                'title' => 'Nuevo horario',
                'errors' => $errors,
                'nombre' => $name,
                'tienda_id' => $tiendaId,
                'tiendas' => Tienda::allByUser($userId),
            ]);
            return;
        }

        Schedule::create($userId, $tiendaId, $name, $blocks);

        header('Location: /schedules');
        exit;
    }

    public static function edit(Router $router): void
    {
        requireAuth();

        $userId = (int) $_SESSION['user_id'];
        $schedule = Schedule::find((int) ($_GET['id'] ?? 0), $userId);

        if (!$schedule) {
            header('Location: /schedules');
            exit;
        }

        $router->render('schedules/Edit', [
            'title' => 'Editar horario',
            'schedule' => $schedule,
            'tiendas' => Tienda::allByUser($userId),
        ]);
    }

    public static function update(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $userId = (int) $_SESSION['user_id'];
        $schedule = Schedule::find($id, $userId);

        if (!$schedule) {
            header('Location: /schedules');
            exit;
        }

        $name = trim($_POST['nombre'] ?? '');
        $tiendaId = (int) ($_POST['tienda_id'] ?? 0);

        [$errors, $blocks] = self::validate($name, $_POST['hora_inicio'] ?? [], $_POST['hora_fin'] ?? []);

        if ($tiendaId <= 0 || !Tienda::find($tiendaId, $userId)) {
            $errors[] = 'Selecciona una tienda válida.';
        }

        if (!empty($errors)) {
            $router->render('schedules/Edit', [
                'title' => 'Editar horario',
                'schedule' => $schedule,
                'errors' => $errors,
                'nombre' => $name,
                'tiendas' => Tienda::allByUser($userId),
            ]);
            return;
        }

        Schedule::update($id, $userId, $tiendaId, $name, $blocks);

        header('Location: /schedules');
        exit;
    }

    public static function destroy(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        Schedule::delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);

        header('Location: /schedules');
        exit;
    }

    private static function validate(string $name, array $starts, array $ends): array
    {
        $errors = [];

        if ($name === '') {
            $errors[] = 'El nombre del horario es obligatorio.';
        }

        [$blockErrors, $blocks] = OvertimeCalculator::parseBlocks($starts, $ends);
        $errors = array_merge($errors, $blockErrors);

        if (empty($blocks) && empty($blockErrors)) {
            $errors[] = 'Agrega al menos un bloque de horario válido.';
        }

        return [$errors, $blocks];
    }
}