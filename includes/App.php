<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
//dirname me ayuda a ir un nivel arriba de la carpeta actual, 
// en este caso public, para poder acceder a la carpeta vendor y cargar el autoload.php que genera composer

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

session_start();