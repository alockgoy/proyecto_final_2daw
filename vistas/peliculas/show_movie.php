<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';

// Crear instancia del controlador
$controller = new MovieController();

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una película.');
}

$id = $_GET['id'];
$movie = $controller->getMovie($id);

// Si la película no existe, mostrar error
if (!$movie) {
    die('Error: La película solicitada no existe.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la película seleccionada -->
    <title><?php echo htmlspecialchars($movie['name']); ?></title>
</head>
<body>

    <!-- Título de la película seleccionada -->
    <h1><?php echo htmlspecialchars($movie['name']); ?></h1>
    
    <!-- Poster de la película seleccionada -->
    <div>
        <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['name']); ?>" width="300">
    </div>
    
    <div>
        <h2>Detalles de la película</h2>
        <p><strong>Director:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
        <p><strong>Año:</strong> <?php echo htmlspecialchars($movie['year']); ?></p>
        <p><strong>Género:</strong> <?php echo htmlspecialchars($movie['gender']); ?></p>
        <p><strong>Idiomas:</strong> <?php echo htmlspecialchars($movie['languages']); ?></p>
        <p><strong>Calidad:</strong> <?php echo htmlspecialchars($movie['quality']); ?></p>
        <p><strong>Tamaño:</strong> <?php echo htmlspecialchars($movie['size']); ?> GB</p>
        
        <?php if (!empty($movie['rating'])): ?>
            <p><strong>Calificación:</strong> <?php echo htmlspecialchars($movie['rating']); ?>/10</p>
        <?php endif; ?>
        
        <?php if (!empty($movie['backup'])): ?>
            <p><strong>Backup:</strong> <?php echo htmlspecialchars($movie['backup']); ?></p>
        <?php endif; ?>
        
        <p><strong>En servidor:</strong> <?php echo $movie['server'] == 'si' ? 'Sí' : 'No'; ?></p>
    </div>
    
    <div>
        <h2>Sinopsis</h2>
        <p><?php echo htmlspecialchars($movie['synopsis']); ?></p>
    </div>
    
    <div>
        <h3>Acciones</h3>
        <a href="edit_movie.php?id=<?php echo $movie['id_movie']; ?>">Editar</a> |
        <a href="delete_movie.php?id=<?php echo $movie['id_movie']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta película?')">Eliminar</a> |
        <a href="movies.php">Volver al listado</a>
    </div>
</body>
</html>