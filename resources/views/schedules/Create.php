<h2 class="text-2xl font-bold mb-4">Nuevo horario</h2>

<?php if (!empty($errors)): ?>
    <ul class="text-red-600 mb-4">
        <?php foreach ($errors as $error): ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (empty($tiendas)): ?>
    <p class="text-red-600 mb-4">
        Primero debes crear una tienda. <a href="/tiendas/create" class="underline">Crear tienda</a>
    </p>
<?php else: ?>
<form method="POST" action="/schedules" class="space-y-4 max-w-md">
    <?php echo csrfField(); ?>

    <div>
        <label class="block font-medium">Empresa</label>
        <select name="tienda_id" class="border rounded w-full p-2" required>
            <option value="">-- Selecciona una empresa --</option>
            <?php foreach ($tiendas as $tienda): ?>
                <option value="<?php echo (int) $tienda['id']; ?>" <?php echo ((int) $tienda['id'] === (int) ($tienda_id ?? 0)) ? 'selected' : ''; ?>>
                    <?php echo e($tienda['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label class="block font-medium">Nombre del horario (Tienda donde trabajas)</label>
        <input type="text" name="nombre" value="<?php echo e($nombre ?? ''); ?>" class="border rounded w-full p-2" required>
    </div>

    <fieldset class="border p-3 rounded">
        <legend class="font-medium">Bloque 1 (obligatorio)</legend>
        <label class="block text-sm">Hora inicio</label>
        <input type="time" name="hora_inicio[]" class="border rounded w-full p-2 mb-2" required>
        <label class="block text-sm">Hora fin</label>
        <input type="time" name="hora_fin[]" class="border rounded w-full p-2" required>
    </fieldset>

    <fieldset class="border p-3 rounded">
        <legend class="font-medium">Bloque 2 (opcional — para turno partido)</legend>
        <label class="block text-sm">Hora inicio</label>
        <input type="time" name="hora_inicio[]" class="border rounded w-full p-2 mb-2">
        <label class="block text-sm">Hora fin</label>
        <input type="time" name="hora_fin[]" class="border rounded w-full p-2">
    </fieldset>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
</form>
<?php endif; ?>