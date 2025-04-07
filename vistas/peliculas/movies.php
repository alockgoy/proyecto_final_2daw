<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

// Crear instancia del controlador de usuarios
$userController = new UserController();

// Obtener ID del usuario por su nombre de usuario
$userId = $userController->getUserIdByUsername($_SESSION['username']);

if (!$userId) {
    // Si no se encuentra el usuario, cerrar la sesión y volver al index
    session_destroy();
    header("Location: ../../index.html");
    exit();
}

// Crear instancia del controlador
$movieController = new MovieController();

// Obtener las películas vinculadas al usuario actual
$movies = $movieController->getMoviesByUserId($userId);
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