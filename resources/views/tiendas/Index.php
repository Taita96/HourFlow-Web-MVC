<h2 class="text-2xl font-bold mb-4">Empresas y sucursales</h2>

<a href="/tiendas/create" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded">
    Agregar empresa o sucursal
</a>

<?php if (empty($tiendas)): ?>
    <p>Todavía no tienes empresas o sucursales creadas.</p>
<?php else: ?>
    <ul class="space-y-2">
        <?php foreach ($tiendas as $tienda): ?>
            <li class="border p-3 rounded flex items-center justify-between">
                <span><?php echo e($tienda['nombre']); ?></span>
                <div class="flex gap-3 items-center">
                    <a href="/tiendas/edit?id=<?php echo (int) $tienda['id']; ?>" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="/tiendas/delete" onsubmit="return confirm('Esto también borrará todos los horarios y registros de esta tienda. ¿Seguro?');">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="id" value="<?php echo (int) $tienda['id']; ?>">
                        <button type="submit" class="text-red-600 hover:underline">Borrar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>