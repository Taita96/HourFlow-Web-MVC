<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;

class IndexController
{
    public static function index(Router $router): void
    {
        $router->render('Home',[
            'title' => 'index',
        ]);
    }
}