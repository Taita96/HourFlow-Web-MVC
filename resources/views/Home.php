<h2 class="text-3xl font-bold text-blue-600 mb-4">Bienvenido a HourFlow, <?php echo e($_SESSION['user_name'] ?? ''); ?></h2>

<div class="bg-white p-4 rounded shadow mb-6">
    <h3 class="font-semibold mb-2">Resumen de este mes</h3>
    <?php if ($recordCount === 0): ?>
        <p class="text-gray-500">Todavía no registraste horas este mes.</p>
    <?php else: ?>
        <div class="flex gap-6 text-sm">
            <span>Planeado: <strong><?php echo e($totals['planned_label']); ?></strong></span>
            <span>Trabajado: <strong><?php echo e($totals['worked_label']); ?></strong></span>
            <span class="<?php echo $totals['extra_positive'] ? 'text-green-600' : 'text-red-600'; ?>">
                <?php echo $totals['extra_positive'] ? 'Extra' : 'Faltante'; ?>: <strong><?php echo e($totals['extra_label']); ?></strong>
            </span>
        </div>
    <?php endif; ?>
</div>

<div class="flex gap-4">
    <a href="/records/create" class="px-4 py-2 bg-blue-600 text-white rounded">Registrar día</a>
    <a href="/records" class="px-4 py-2 bg-white border rounded">Ver todo</a>
</div>