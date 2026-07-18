<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #eff6ff;
        }

        .totals {
            margin-top: 16px;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <h1>HourFlow — Resumen de <?php echo e($month); ?><?php echo $tienda ? ' — ' . e($tienda['nombre']) : ' — Todas las tiendas'; ?></h1>
    <p><?php echo e($_SESSION['user_name'] ?? ''); ?></p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tienda</th>
                <th>Planeado</th>
                <th>Trabajado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo e($record['fecha']); ?></td>
                    <td><?php echo e($record['tienda_nombre']); ?></td>
                    <td>
                        <?php if ($record['planned_range']): ?>
                            <?php echo e($record['planned_range']); ?> - <?php echo e($record['planned_label']); ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($record['worked_range']); ?> - <?php echo e($record['worked_label']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <p>Planeado del mes: <strong><?php echo e($totals['planned_label']); ?></strong></p>
        <p>Trabajado del mes: <strong><?php echo e($totals['worked_label']); ?></strong></p>
        <p><?php echo $totals['extra_positive'] ? 'Extra' : 'Faltante'; ?>: <strong><?php echo e($totals['extra_label']); ?></strong></p>
    </div>
</body>

</html>