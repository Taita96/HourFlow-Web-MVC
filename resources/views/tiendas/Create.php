<h2 class="text-2xl font-bold mb-4">Nueva empresa</h2>

<?php if (!empty($error)): ?>
    <p class="text-red-600 mb-4"><?php echo e($error); ?></p>
<?php endif; ?>

<form method="POST" action="/tiendas" class="space-y-4 max-w-md">
    <?php echo csrfField(); ?>
    <div>
        <label class="block font-medium">Nombre de la empresa</label>
        <input type="text" name="nombre" value="<?php echo e($nombre ?? ''); ?>" class="border rounded w-full p-2" required>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
</form>