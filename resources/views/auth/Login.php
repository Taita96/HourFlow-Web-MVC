<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Iniciar sesión</h2>

    <?php if (!empty($error)): ?>
        <p class="text-red-600 mb-4"><?php echo e($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="/login" class="space-y-4">
        <?php echo csrfField(); ?>
        <div>
            <label class="block font-medium mb-1">Email</label>
            <input type="email" name="email" class="border rounded w-full p-2" required>
        </div>
        <div>
            <label class="block font-medium mb-1">Contraseña</label>
            <input type="password" name="password" class="border rounded w-full p-2" required>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Entrar</button>
    </form>

    <p class="mt-4 text-sm"><a href="/register" class="text-blue-600 hover:underline">Crear cuenta</a></p>
</div>