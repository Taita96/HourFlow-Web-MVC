<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
//dirname me ayuda a ir un nivel arriba de la carpeta actual, 
// en este caso public, para poder acceder a la carpeta vendor y cargar el autoload.php que genera composer

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

session_start();


// Convierte warnings/notices de PHP en excepciones reales, para que
// el manejador de abajo los capture igual que a cualquier otro error.
set_error_handler(function (int $severity, string $message, string $file, int $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    http_response_code(500);

    if (($_ENV['APP_ENV'] ?? 'production') !== 'production') {
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }

    include dirname(__DIR__) . '/resources/views/errors/500.php';
});