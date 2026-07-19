<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/build/css/app.css?v=<?php echo @filemtime(dirname(__DIR__, 2) . '/public/build/css/app.css') ?: time(); ?>">
    <title><?php echo $title ?? 'HourFlow'; ?></title>
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold">HourFlow</a>

            <!-- Escritorio: siempre en fila desde 640px. JS nunca toca este bloque. -->
            <div class="hidden sm:flex gap-4 items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/" class="hover:underline">Inicio</a>
                    <a href="/tiendas" class="hover:underline">Empresas</a>
                    <a href="/schedules" class="hover:underline">Horarios</a>
                    <a href="/records" class="hover:underline">Horas trabajadas</a>
                    <span class="text-sm opacity-80">Hola, <?php echo e($_SESSION['user_name'] ?? ''); ?></span>
                    <a href="/logout" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100">Cerrar sesión</a>
                <?php else: ?>
                    <a href="/login" class="hover:underline">Iniciar sesión</a>
                    <a href="/register" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100">Crear cuenta</a>
                <?php endif; ?>
            </div>

            <!-- Botón hamburguesa: solo existe por debajo de 640px -->
            <button id="nav-toggle" type="button" class="sm:hidden p-1" aria-label="Abrir menú" aria-expanded="false" aria-controls="nav-links-mobile">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile: "sm:hidden" lo apaga en escritorio pase lo que pase con el JS -->
        <div id="nav-links-mobile" class="hidden sm:hidden flex flex-col gap-1.5 px-4 pb-4 text-sm">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/" class="hover:underline block">Inicio</a>
                <a href="/tiendas" class="hover:underline block">Empresas</a>
                <a href="/schedules" class="hover:underline block">Horarios</a>
                <a href="/records" class="hover:underline block">Horas trabajadas</a>
                <span class="opacity-80 block mt-3">Hola, <?php echo e($_SESSION['user_name'] ?? ''); ?></span>
                <a href="/logout" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100 inline-block w-fit">Cerrar sesión</a>
            <?php else: ?>
                <a href="/login" class="hover:underline block">Iniciar sesión</a>
                <a href="/register" class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100 inline-block w-fit">Crear cuenta</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-4 sm:p-6">
        <?php echo $content ?? ''; ?>
    </main>

    <script src="/build/js/app.js?v=<?php echo @filemtime(dirname(__DIR__, 2) . '/public/build/js/app.js') ?: time(); ?>" defer></script>
</body>

</html>