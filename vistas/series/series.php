<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';
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
$controller = new SerieController();

// Obtener las series vinculadas al usuario actual
$series = $controller->getSeriesByUserId($userId);
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