<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
//dirname me ayuda a ir un nivel arriba de la carpeta actual, 
// en este caso public, para poder acceder a la carpeta vendor y cargar el autoload.php que genera composer

use App\Database\Database;
use  Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

header('Content-Type: application/json; charset=utf-8');

try {
    Database::connect();
    echo "OK: conexión a la base de datos exitosa." . "\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage() . "\n";
}

