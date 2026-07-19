<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
    <h2 class="text-2xl font-bold">Mis horas trabajadas</h2>
    <form method="GET" action="/records" class="flex flex-wrap gap-2 sm:gap-4">
        <select name="tienda" class="border rounded p-1" onchange="this.form.submit()">
            <option value="">Todas las empresas</option>
            <?php foreach ($tiendas as $tienda): ?>
                <option value="<?php echo (int) $tienda['id']; ?>" <?php echo ((int) $tienda['id'] === $tiendaId) ? 'selected' : ''; ?>>
                    <?php echo e($tienda['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="month" name="mes" value="<?php echo e($month); ?>" class="border rounded p-1" onchange="this.form.submit()">
    </form>
</div>

<div class="flex gap-3 mb-4">
    <a href="/records/export/pdf?mes=<?php echo e($month); ?>&tienda=<?php echo (int) ($tiendaId ?? 0); ?>" class="text-sm px-3 py-1 border rounded hover:bg-gray-50">Exportar PDF</a>
    <a href="/records/export/excel?mes=<?php echo e($month); ?>&tienda=<?php echo (int) ($tiendaId ?? 0); ?>" class="text-sm px-3 py-1 border rounded hover:bg-gray-50">Exportar Excel</a>
</div>

<div class="bg-white p-4 rounded shadow mb-4">
    <div class="flex flex-wrap gap-4 text-sm">
        <span>Planeado del mes: <strong><?php echo e($totals['planned_label']); ?></strong></span>
        <span>Trabajado del mes: <strong><?php echo e($totals['worked_label']); ?></strong></span>
        <span class="<?php echo $totals['extra_positive'] ? 'text-green-600' : 'text-red-600'; ?>">
            <?php echo $totals['extra_positive'] ? 'Extra' : 'Faltante'; ?>: <strong><?php echo e($totals['extra_label']); ?></strong>
        </span>
    </div>
</div>

<div class="bg-white p-4 rounded shadow mb-6">
    <canvas id="overtimeChart" data-month="<?php echo e($month); ?>"></canvas>
</div>

<a href="/records/create" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded">Registrar día</a>

<?php if (empty($records)): ?>
    <p>Todavía no tienes registros.</p>
<?php else: ?>
    <ul class="space-y-2">
        <?php foreach ($records as $record): ?>
            <li class="border p-3 rounded flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <span>
                    <?php echo e($record['fecha']); ?>
                    <span class="text-sm text-gray-500">
                        · <?php echo e($record['tienda_nombre']); ?>
                        <?php if ($record['horario_nombre']): ?>
                            · <?php echo e($record['horario_nombre']); ?>
                        <?php endif; ?>
                    </span>
                </span>

                <div class="text-sm flex flex-wrap gap-3 sm:gap-4">
                    <span>Trabajado: <?php echo e($record['worked_label']); ?></span>
                    <?php if ($record['planned_label'] !== null): ?>
                        <span>Planeado: <?php echo e($record['planned_label']); ?></span>
                        <span class="<?php echo $record['extra_class']; ?>"><?php echo e($record['extra_label']); ?></span>
                    <?php else: ?>
                        <span class="text-gray-400">(sin horario asignado, no se calcula extra)</span>
                    <?php endif; ?>
                </div>

                <div class="flex gap-3 items-center">
                    <a href="/records/edit?id=<?php echo (int) $record['id']; ?>" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="/records/delete" onsubmit="return confirm('¿Borrar este registro?');">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="id" value="<?php echo (int) $record['id']; ?>">
                        <button type="submit" class="text-red-600 hover:underline">Borrar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>