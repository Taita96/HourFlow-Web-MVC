<h2 class="text-2xl font-bold mb-4">Mis horarios</h2>

<a href="/schedules/create" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded">Nuevo horario</a>

<?php if (empty($schedules)): ?>
    <p>Todavía no tienes horarios creados.</p>
<?php else: ?>
    <ul class="space-y-2">
        <?php foreach ($schedules as $schedule): ?>
            <li class="border p-3 rounded flex items-center justify-between">
               <span><?php echo e($schedule['nombre']); ?> <span class="text-sm text-gray-500">(<?php echo e($schedule['tienda_nombre']); ?>)</span></span>
                <div class="flex gap-3 items-center">
                    <a href="/schedules/edit?id=<?php echo (int) $schedule['id']; ?>" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="/schedules/delete" onsubmit="return confirm('¿Seguro que quieres borrar este horario?');">
                        <input type="hidden" name="id" value="<?php echo (int) $schedule['id']; ?>">
                        <button type="submit" class="text-red-600 hover:underline">Borrar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>