<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Tienda;
use App\Services\OvertimeCalculator;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController
{
    public static function pdf(): void
    {
        requireAuth();

        [$month, $records, $totals, $tienda] = self::monthData();

        ob_start();
        include dirname(__DIR__, 2) . '/resources/views/exports/MonthlyPdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $suffix = $tienda ? '-' . preg_replace('/[^a-z0-9]+/i', '_', $tienda['nombre']) : '';

        $dompdf->stream("hourflow-{$month}{$suffix}.pdf", ['Attachment' => true]);
    }

public static function excel(): void
{
    requireAuth();

    [$month, $records, $totals, $tienda] = self::monthData();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Resumen ' . $month);

    $sheet->setCellValue('A1', 'Fecha');
    $sheet->setCellValue('B1', 'Tienda');
    $sheet->setCellValue('C1', 'Planeado');
    $sheet->setCellValue('D1', 'Trabajado');
    $sheet->getStyle('A1:D1')->getFont()->setBold(true);

    $row = 2;
    foreach ($records as $record) {
        $planeado = $record['planned_range']
            ? $record['planned_range'] . ' - ' . $record['planned_label']
            : '—';

        $sheet->setCellValue("A{$row}", $record['fecha']);
        $sheet->setCellValue("B{$row}", $record['tienda_nombre']);
        $sheet->setCellValue("C{$row}", $planeado);
        $sheet->setCellValue("D{$row}", $record['worked_range'] . ' - ' . $record['worked_label']);
        $row++;
    }

    $row++;
    $sheet->setCellValue("A{$row}", 'Planeado del mes');
    $sheet->setCellValue("B{$row}", $totals['planned_label']);
    $row++;
    $sheet->setCellValue("A{$row}", 'Trabajado del mes');
    $sheet->setCellValue("B{$row}", $totals['worked_label']);
    $row++;
    $sheet->setCellValue("A{$row}", $totals['extra_positive'] ? 'Extra' : 'Faltante');
    $sheet->setCellValue("B{$row}", $totals['extra_label']);

    foreach (range('A', 'D') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $suffix = $tienda ? '-' . preg_replace('/[^a-z0-9]+/i', '_', $tienda['nombre']) : '';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="hourflow-' . $month . $suffix . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

    private static function monthData(): array
    {
        $userId = (int) $_SESSION['user_id'];
        $month = $_GET['mes'] ?? date('Y-m');

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $tiendaId = null;
        if (!empty($_GET['tienda'])) {
            $candidate = (int) $_GET['tienda'];
            $tiendaId = Tienda::find($candidate, $userId) ? $candidate : null;
        }

        $records = WorkRecordController::monthRecords($userId, $month, $tiendaId);
        $totals = OvertimeCalculator::totalsForRecords($records);
        $tienda = $tiendaId ? Tienda::find($tiendaId, $userId) : null;

        return [$month, $records, $totals, $tienda ?: null];
    }
}