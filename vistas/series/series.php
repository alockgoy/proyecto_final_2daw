<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';

// Crear instancia del controlador
$controller = new SerieController();

// Obtener todas las series
$series = $controller->index();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series</title>
</head>
<body>
    <div>
        <a href="add_serie.php">Añadir Nueva Serie</a>
    </div>
    
    <div>
        <h2>Listado de Series</h2>
        
        <?php if (empty($series)): ?>
            <p>No hay series disponibles.</p>
        <?php else: ?>
            <div>
                <?php foreach ($series as $serie): ?>
                    <div>
                        <img src="../../<?php echo htmlspecialchars($serie['poster']); ?>" alt="<?php echo htmlspecialchars($serie['name']); ?>" width="200">
                        <div><?php echo htmlspecialchars($serie['name']); ?></div>
                        <a target="_blank" href="./show_serie.php?id=<?php echo urlencode($serie['id_serie']); ?>">Ver detalles</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div>
        <a href="../../index.html">Volver al inicio</a> | 
        <a href="../peliculas/movies.php">Ver películas</a>
    </div>
</body>
</html>