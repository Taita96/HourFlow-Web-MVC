<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/build/css/app.css">
    <title><?php echo $title ?? 'HourFlow'; ?></title>
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white px-6 py-4 flex items-center justify-between">
        <a href="/" class="text-xl font-bold">HourFlow</a>

        <div class="flex gap-4 items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/" class="hover:underline">Inicio</a>
                <a href="/tiendas" class="hover:underline">Empresas y sucursales</a>
                <a href="/schedules" class="hover:underline">Horarios</a>
                <a href="/records" class="hover:underline">Horas trabajadas</a>
                <span class="text-sm opacity-80">Hola, <?php echo e($_SESSION['user_name'] ?? ''); ?></span>
                <a href="/logout" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100">Cerrar sesión</a>
            <?php else: ?>
                <a href="/login" class="hover:underline">Iniciar sesión</a>
                <a href="/register" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100">Crear cuenta</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-6">
        <?php echo $content ?? ''; ?>
    </main>

    <script src="/build/js/app.js" defer></script>
</body>

</html>