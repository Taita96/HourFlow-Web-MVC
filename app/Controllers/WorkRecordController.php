<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Models\Schedule;
use App\Models\Tienda;
use App\Models\WorkRecord;
use App\Services\OvertimeCalculator;

class WorkRecordController
{
    public static function index(Router $router): void
    {
        requireAuth();

        $userId = (int) $_SESSION['user_id'];
        $month = self::resolveMonth($_GET['mes'] ?? null);
        $tiendaId = self::resolveTienda($_GET['tienda'] ?? null, $userId);

        $records = self::monthRecords($userId, $month, $tiendaId);
        $totals = OvertimeCalculator::totalsForRecords($records);

        $router->render('records/Index', [
            'title' => 'Mis horas trabajadas',
            'records' => array_reverse($records),
            'month' => $month,
            'totals' => $totals,
            'tiendas' => Tienda::allByUser($userId),
            'tiendaId' => $tiendaId,
        ]);
    }

    public static function create(Router $router): void
    {
        requireAuth();

        $userId = (int) $_SESSION['user_id'];

        $router->render('records/Create', [
            'title' => 'Registrar día trabajado',
            'schedules' => Schedule::allByUser($userId),
            'tiendas' => Tienda::allByUser($userId),
        ]);
    }

    public static function store(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        $userId = (int) $_SESSION['user_id'];
        $date = $_POST['fecha'] ?? '';
        $tiendaId = (int) ($_POST['tienda_id'] ?? 0);
        $scheduleId = ($_POST['horario_id'] ?? '') !== '' ? (int) $_POST['horario_id'] : null;

        [$errors, $blocks] = self::validate($date, $_POST['hora_inicio'] ?? [], $_POST['hora_fin'] ?? []);

        if ($tiendaId <= 0 || !Tienda::find($tiendaId, $userId)) {
            $errors[] = 'Selecciona una tienda válida.';
        }

        if ($scheduleId !== null) {
            $schedule = Schedule::find($scheduleId, $userId);
            if (!$schedule || (int) $schedule['tienda_id'] !== $tiendaId) {
                $errors[] = 'El horario seleccionado no pertenece a esa tienda.';
            }
        }

        if (empty($errors) && WorkRecord::findByDate($date, $userId, $tiendaId)) {
            $errors[] = 'Ya existe un registro para ese día en esa tienda. Edítalo desde la lista en vez de crear uno nuevo.';
        }

        if (!empty($errors)) {
            $router->render('records/Create', [
                'title' => 'Registrar día trabajado',
                'schedules' => Schedule::allByUser($userId),
                'tiendas' => Tienda::allByUser($userId),
                'errors' => $errors,
                'fecha' => $date,
            ]);
            return;
        }

        WorkRecord::create($userId, $tiendaId, $date, $scheduleId, $blocks);

        header('Location: /records');
        exit;
    }

    public static function edit(Router $router): void
    {
        requireAuth();

        $userId = (int) $_SESSION['user_id'];
        $record = WorkRecord::find((int) ($_GET['id'] ?? 0), $userId);

        if (!$record) {
            header('Location: /records');
            exit;
        }

        $router->render('records/Edit', [
            'title' => 'Editar registro',
            'record' => $record,
            'schedules' => Schedule::allByUser($userId),
            'tiendas' => Tienda::allByUser($userId),
        ]);
    }

    public static function update(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        $userId = (int) $_SESSION['user_id'];
        $id = (int) ($_POST['id'] ?? 0);
        $record = WorkRecord::find($id, $userId);

        if (!$record) {
            header('Location: /records');
            exit;
        }

        $date = $_POST['fecha'] ?? '';
        $tiendaId = (int) ($_POST['tienda_id'] ?? 0);
        $scheduleId = ($_POST['horario_id'] ?? '') !== '' ? (int) $_POST['horario_id'] : null;

        [$errors, $blocks] = self::validate($date, $_POST['hora_inicio'] ?? [], $_POST['hora_fin'] ?? []);

        if ($tiendaId <= 0 || !Tienda::find($tiendaId, $userId)) {
            $errors[] = 'Selecciona una tienda válida.';
        }

        if ($scheduleId !== null) {
            $schedule = Schedule::find($scheduleId, $userId);
            if (!$schedule || (int) $schedule['tienda_id'] !== $tiendaId) {
                $errors[] = 'El horario seleccionado no pertenece a esa tienda.';
            }
        }

        $existing = WorkRecord::findByDate($date, $userId, $tiendaId);
        if (empty($errors) && $existing && (int) $existing['id'] !== $id) {
            $errors[] = 'Ya existe otro registro para ese día en esa tienda.';
        }

        if (!empty($errors)) {
            $router->render('records/Edit', [
                'title' => 'Editar registro',
                'record' => $record,
                'schedules' => Schedule::allByUser($userId),
                'tiendas' => Tienda::allByUser($userId),
                'errors' => $errors,
            ]);
            return;
        }

        WorkRecord::update($id, $userId, $tiendaId, $date, $scheduleId, $blocks);

        header('Location: /records');
        exit;
    }

    public static function destroy(Router $router): void
    {
        requireAuth();
        verifyCsrf();

        WorkRecord::delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);

        header('Location: /records');
        exit;
    }

    public static function chartData(): void
    {
        requireAuth();

        $userId = (int) $_SESSION['user_id'];
        $month = self::resolveMonth($_GET['mes'] ?? null);
        $tiendaId = self::resolveTienda($_GET['tienda'] ?? null, $userId);

        $records = self::monthRecords($userId, $month, $tiendaId);

        $data = array_map(static function (array $record): array {
            return [
                'fecha' => $record['fecha'],
                'planeado_horas' => round($record['planned_minutes'] / 60, 2),
                'trabajado_horas' => round($record['worked_minutes'] / 60, 2),
            ];
        }, $records);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    public static function monthRecords(int $userId, string $month, ?int $tiendaId = null): array
    {
        [$start, $end] = self::monthRange($month);

        return self::withSummaries(WorkRecord::allByUserForMonth($userId, $start, $end, $tiendaId), $userId);
    }

    private static function withSummaries(array $records, int $userId): array
    {
        foreach ($records as &$record) {
            $workedBlocks = WorkRecord::blocksFor((int) $record['id']);
            $plannedBlocks = $record['horario_id'] ? Schedule::blocksFor((int) $record['horario_id'], $userId) : [];

            $summary = OvertimeCalculator::summarize($plannedBlocks, $workedBlocks);

            $record['worked_minutes'] = $summary['worked_minutes'];
            $record['planned_minutes'] = $summary['planned_minutes'];
            $record['worked_label'] = OvertimeCalculator::format($summary['worked_minutes']);
            $record['worked_range'] = OvertimeCalculator::formatBlocks($workedBlocks);

            if ($record['horario_id']) {
                $record['planned_label'] = OvertimeCalculator::format($summary['planned_minutes']);
                $record['planned_range'] = OvertimeCalculator::formatBlocks($plannedBlocks);
                $extra = $summary['extra_minutes'];
                $record['extra_label'] = ($extra >= 0 ? 'Extra: ' : 'Faltante: ') . OvertimeCalculator::format(abs($extra));
                $record['extra_class'] = $extra > 0 ? 'text-green-600 font-semibold' : ($extra < 0 ? 'text-red-600' : 'text-gray-500');
            } else {
                $record['planned_label'] = null;
                $record['planned_range'] = null;
            }
        }
        unset($record);

        return $records;
    }

    private static function resolveMonth(?string $month): string
    {
        if ($month !== null && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $month;
        }

        return date('Y-m');
    }

    private static function resolveTienda(?string $tiendaId, int $userId): ?int
    {
        if ($tiendaId === null || $tiendaId === '') {
            return null;
        }

        $id = (int) $tiendaId;

        return Tienda::find($id, $userId) ? $id : null;
    }

    private static function monthRange(string $month): array
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        return [$start, $end];
    }

    private static function validate(string $date, array $starts, array $ends): array
    {
        $errors = [];

        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors[] = 'La fecha es obligatoria y debe ser válida.';
        }

        [$blockErrors, $blocks] = OvertimeCalculator::parseBlocks($starts, $ends);
        $errors = array_merge($errors, $blockErrors);

        if (empty($blocks) && empty($blockErrors)) {
            $errors[] = 'Agrega al menos un bloque de horas trabajadas.';
        }

        return [$errors, $blocks];
    }
}
