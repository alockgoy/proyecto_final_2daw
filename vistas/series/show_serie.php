<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';

// Crear instancia del controlador
$controller = new SerieController();

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una serie.');
}

$id = $_GET['id'];
$serie = $controller->getSerie($id);

// Si la serie no existe, mostrar error
if (!$serie) { 
    die('Error: La serie solicitada no existe.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($serie['name']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($serie['name']); ?></h1>
    
    <!-- Póster de la serie -->
    <div>
        <img src="../../<?php echo htmlspecialchars($serie['poster']); ?>" alt="<?php echo htmlspecialchars($serie['name']); ?>" width="300">
    </div>
    
    <!-- Detalles de la serie -->
    <div>
        <h2>Detalles de la serie</h2>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($serie['name']); ?></p>
        <p><strong>Género:</strong> <?php echo htmlspecialchars($serie['gender']); ?></p>
        <p><strong>Idiomas:</strong> <?php echo htmlspecialchars($serie['languages']); ?></p>
        <p><strong>Temporadas:</strong> <?php echo htmlspecialchars($serie['seasons']); ?></p>
        <p><strong>Completa:</strong> <?php echo $serie['complete'] == 'si' ? 'Sí' : 'No'; ?></p>
        <p><strong>Año:</strong> <?php echo htmlspecialchars($serie['year']); ?></p>
        <p><strong>Calidad:</strong> <?php echo htmlspecialchars($serie['quality']); ?></p>
        <p><strong>Tamaño:</strong> <?php echo htmlspecialchars($serie['size']); ?> MB</p>
        <p><strong>En servidor:</strong> <?php echo $serie['server'] == 'si' ? 'Sí' : 'No'; ?></p>
        
        <?php if (!empty($serie['rating'])): ?>
            <p><strong>Calificación:</strong> <?php echo htmlspecialchars($serie['rating']); ?>/10</p>
        <?php endif; ?>
        
        <?php if (!empty($serie['backup'])): ?>
            <p><strong>Backup: </strong><?php echo htmlspecialchars($serie['backup']); ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Enlaces de acción -->
    <div>
        <h3>Acciones</h3>
        <a href="edit_serie.php?id=<?php echo $serie['id_serie']; ?>">Editar</a> |
        <a href="delete_serie.php?id=<?php echo $serie['id_serie']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta serie?')">Eliminar</a> |
        <a href="series.php">Volver al listado</a>
    </div>
</body>
</html>