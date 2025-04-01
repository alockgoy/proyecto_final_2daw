<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';

// Crear instancia del controlador
$controller = new MovieController();

// Obtener todas las películas
$movies = $controller->index();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas - Biblioteca Multimedia</title>
</head>
<body>
    <h1>Biblioteca de Películas</h1>
    
    <div>
        <a href="add_movie.php">Añadir Nueva Película</a>
    </div>
    
    <div>
        <h2>Listado de Películas</h2>
        
        <!-- Comprobar que hay al menos una pelícuña -->
        <?php if (empty($movies)): ?>
            <p>No hay películas disponibles.</p>
        <?php else: ?>
            <div>
                <?php foreach ($movies as $movie): ?>
                    <div>
                        <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['name']); ?>" width="200">
                        <div><?php echo htmlspecialchars($movie['name']); ?></div>
                        <a target="_blank" href="./show_movie.php?id=<?php echo urlencode($movie['id_movie']); ?>">Ver detalles</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div>
        <a href="../../index.html">Volver al inicio</a>
    </div>
</body>
</html>