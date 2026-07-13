<?php

declare(strict_types=1);

namespace App\Core;

class Router
{

    protected array $routesGet = [];
    protected array $routesPost = [];

    public function get(string $url, callable $function): void
    {
        $this->routesGet[$url] = $function;
    }

    public function post(string $url, callable $function): void
    {
        $this->routesPost[$url] = $function;
    }

    public function resolve(): void
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $url = rtrim($url, '/');

        if ($url === '') {
            $url = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        $routes = $method === 'POST' ? $this->routesPost : $this->routesGet;

        $fn = $routes[$url] ?? null;

        if ($fn) {
            call_user_func($fn, $this);
        } else {
            http_response_code(404);
            echo "Page not found";
        }
    }

    public function render(string $view, array $data = []): void
    {

        foreach ($data as $key => $value) {
            $$key = $value; // variable variable: crea una variable cuyo nombre es el valor de $key
        }

        ob_start(); // inicia el almacenamiento en búfer de salida
        include_once dirname(__DIR__, 2) . "/resources/views/{$view}.php"; // incluye la vista
        $content = ob_get_clean(); // obtiene el contenido del búfer y lo limpia

        include_once dirname(__DIR__, 2) . "/resources/views/Layout.php"; // incluye el layout
    }
}
