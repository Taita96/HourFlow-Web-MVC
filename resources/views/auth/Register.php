<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Crear cuenta</h2>

    <?php if (!empty($errores)): ?>
        <ul class="text-red-600 mb-4 list-disc list-inside">
            <?php foreach ($errores as $error): ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/register" class="space-y-4">
        <?php echo csrfField(); ?>
        <div>
            <label class="block font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" value="<?php echo e($nombre ?? ''); ?>" class="border rounded w-full p-2" required>
        </div>
        <div>
            <label class="block font-medium mb-1">Email</label>
            <input type="email" name="email" value="<?php echo e($email ?? ''); ?>" class="border rounded w-full p-2" required>
        </div>
        <div>
            <label class="block font-medium mb-1">Contraseña</label>
            <input type="password" name="password" class="border rounded w-full p-2" required minlength="8">
        </div>
        <div>
            <label class="block font-medium mb-1">Confirmar contraseña</label>
            <input type="password" name="password_confirm" class="border rounded w-full p-2" required minlength="8">
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Registrarme</button>
    </form>

    <p class="mt-4 text-sm"><a href="/login" class="text-blue-600 hover:underline">Ya tengo cuenta</a></p>
</div>