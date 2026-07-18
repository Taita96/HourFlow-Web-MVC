<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/App.php';

use App\Core\Router;
use App\Controllers\IndexController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\ScheduleController;
use App\Controllers\WorkRecordController;
use App\Controllers\ExportController;
use App\Controllers\TiendaController;

$router = new Router();

$router->get('/', [IndexController::class, 'index']);

//LOGIN
$router->get('/login', [LoginController::class, 'show']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

//REGISTER
$router->get('/register', [RegisterController::class, 'show']);
$router->post('/register', [RegisterController::class, 'store']);

//SCHEDULES
$router->get('/schedules', [ScheduleController::class, 'index']);
$router->get('/schedules/create', [ScheduleController::class, 'create']);
$router->post('/schedules', [ScheduleController::class, 'store']);

$router->get('/schedules/edit', [ScheduleController::class, 'edit']);
$router->post('/schedules/update', [ScheduleController::class, 'update']);
$router->post('/schedules/delete', [ScheduleController::class, 'destroy']);

//WORK RECORDS
$router->get('/records', [WorkRecordController::class, 'index']);
$router->get('/records/create', [WorkRecordController::class, 'create']);
$router->post('/records', [WorkRecordController::class, 'store']);
$router->get('/records/edit', [WorkRecordController::class, 'edit']);
$router->post('/records/update', [WorkRecordController::class, 'update']);
$router->post('/records/delete', [WorkRecordController::class, 'destroy']);
$router->get('/records/chart-data', [WorkRecordController::class, 'chartData']);

//EXPORTS
$router->get('/records/export/pdf', [ExportController::class, 'pdf']);
$router->get('/records/export/excel', [ExportController::class, 'excel']);

//TIENDAS
$router->get('/tiendas', [TiendaController::class, 'index']);
$router->get('/tiendas/create', [TiendaController::class, 'create']);
$router->post('/tiendas', [TiendaController::class, 'store']);
$router->get('/tiendas/edit', [TiendaController::class, 'edit']);
$router->post('/tiendas/update', [TiendaController::class, 'update']);
$router->post('/tiendas/delete', [TiendaController::class, 'destroy']);

$router->resolve();

