<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/App.php';

use App\Core\Router;
use App\Controllers\IndexController;

$router = new Router();

$router->get('/', [IndexController::class, 'index']);

$router->resolve();

