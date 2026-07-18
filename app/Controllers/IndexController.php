<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Services\OvertimeCalculator;

class IndexController
{
    public static function index(Router $router): void
    {
        requireAuth();

        $userId = (int) $_SESSION['user_id'];
        $month = date('Y-m');

        $records = WorkRecordController::monthRecords($userId, $month);
        $totals = OvertimeCalculator::totalsForRecords($records);

        $router->render('Home', [
            'title' => 'Inicio',
            'totals' => $totals,
            'recordCount' => count($records),
        ]);
    }
}