<h2 class="text-2xl font-bold mb-4">Registrar día trabajado</h2>

<?php if (!empty($errors)): ?>
    <ul class="text-red-600 mb-4">
        <?php foreach ($errors as $error): ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (empty($tiendas)): ?>
    <p class="text-red-600 mb-4">
        Primero debes añadir un nombre de empresa. <a href="/tiendas/create" class="underline">Añadir nombre de empresa</a>
    </p>
<?php else: ?>
<form method="POST" action="/records" class="space-y-4 max-w-md">
    <?php echo csrfField(); ?>
    <div>
        <label class="block font-medium">Fecha</label>
        <input type="date" name="fecha" value="<?php echo e($fecha ?? ''); ?>" class="border rounded w-full p-2" required>
    </div>

    <div>
        <label class="block font-medium">Empresa</label>
        <select name="tienda_id" id="tienda_id" class="border rounded w-full p-2" required>
            <option value="">-- Selecciona una empresa --</option>
            <?php foreach ($tiendas as $tienda): ?>
                <option value="<?php echo (int) $tienda['id']; ?>"><?php echo e($tienda['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label class="block font-medium">Horario planeado (opcional)</label>
        <select name="horario_id" id="horario_id" class="border rounded w-full p-2">
            <option value="">-- Sin horario asignado --</option>
            <?php foreach ($schedules as $schedule): ?>
                <option value="<?php echo (int) $schedule['id']; ?>" data-tienda-id="<?php echo (int) $schedule['tienda_id']; ?>">
                    <?php echo e($schedule['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="text-sm text-gray-500 mt-1">Solo se muestran los horarios de la tienda seleccionada.</p>
    </div>

    <fieldset class="border p-3 rounded">
        <legend class="font-medium">Horas trabajadas — Bloque 1 (obligatorio)</legend>
        <label class="block text-sm">Hora inicio</label>
        <input type="time" name="hora_inicio[]" class="border rounded w-full p-2 mb-2" required>
        <label class="block text-sm">Hora fin</label>
        <input type="time" name="hora_fin[]" class="border rounded w-full p-2" required>
    </fieldset>

    <fieldset class="border p-3 rounded">
        <legend class="font-medium">Bloque 2 (opcional — si trabajaste partido)</legend>
        <label class="block text-sm">Hora inicio</label>
        <input type="time" name="hora_inicio[]" class="border rounded w-full p-2 mb-2">
        <label class="block text-sm">Hora fin</label>
        <input type="time" name="hora_fin[]" class="border rounded w-full p-2">
    </fieldset>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
</form>
<?php endif; ?>