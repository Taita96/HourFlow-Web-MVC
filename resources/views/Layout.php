<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?? 'HourFlow'; ?></title>
    </head>
    <body>
        <header>
            <h1>HourFlow</h1>
        </header>
        <main>
            <?php echo $content ?? ''; ?>
        </main>
    </body>
</html>