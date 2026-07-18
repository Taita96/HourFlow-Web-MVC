<?php 

declare(strict_types=1);

if(!function_exists('dd')){
    function dd($variable){

        if(($_ENV['APP_ENV'] ?? 'production') === 'production'){
            return;
        }

        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        exit;
    }
}

if(!function_exists('e')){
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('requireAuth')) {
    function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}

if (!function_exists('csrfToken')) {
    function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
    }
}

if (!function_exists('verifyCsrf')) {
    function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';

        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            exit('Token de seguridad inválido. Recarga la página e intenta de nuevo.');
        }
    }
}