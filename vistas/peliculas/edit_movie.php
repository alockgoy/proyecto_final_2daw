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
    die('Error: No se ha especificado una película para editar.');
}

$id = $_GET['id'];
$error = "";

// Obtener los datos actuales de la película
$movie = $controller->getMovie($id);

// Si la película no existe, mostrar error
if (!$movie) {
    die('Error: La película solicitada no existe.');
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Intentar actualizar la película
    try {
        $controller->updateMovie($id);

        // Redirigir a la vista concreta
        header("Location: show_movie.php?id=$id");
        exit;
    } catch (Exception $e) {
        $error = "Error al actualizar la película: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Película - <?php echo htmlspecialchars($movie['name']); ?></title>
</head>
<body>
    <h1>Editar Película: <?php echo htmlspecialchars($movie['name']); ?></h1>
    
    <?php if (!empty($error)): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($movie['name']); ?>" required>
        </div>
        
        <div>
            <label for="synopsis">Sinopsis:</label>
            <textarea id="synopsis" name="synopsis"><?php echo htmlspecialchars($movie['synopsis']); ?></textarea>
        </div>
        
        <div>
            <label for="director">Director:</label>
            <input type="text" id="director" name="director" value="<?php echo htmlspecialchars($movie['director']); ?>" required>
        </div>
        
        <div>
            <label for="gender">Género:</label>
            <select id="gender" name="gender" required>
                <?php
                $genders = ['acción/aventura', 'animación', 'anime', 'ciencia ficción', 'cortometraje', 
                           'comedia', 'deportes', 'documental', 'drama', 'familiar', 'fantasía', 
                           'guerra', 'terror', 'musical', 'suspense', 'romance', 'vaqueros', 'misterio'];
                foreach ($genders as $gender) {
                    $selected = ($movie['gender'] == $gender) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($gender) . "\" $selected>" . 
                         ucfirst(htmlspecialchars($gender)) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div>
            <label for="languages">Idiomas:</label>
            <input type="text" id="languages" name="languages" value="<?php echo htmlspecialchars($movie['languages']); ?>" required>
        </div>
        
        <div>
            <label for="size">Tamaño (MB):</label>
            <input type="number" id="size" name="size" step="0.01" value="<?php echo htmlspecialchars($movie['size']); ?>" required>
        </div>
        
        <div>
            <label for="year">Año:</label>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($movie['year']); ?>" required>
        </div>
        
        <div>
            <label for="quality">Calidad:</label>
            <select id="quality" name="quality" required>
                <?php
                $qualities = ['4K', '1440p', '1080p', '720p', '420p', 'otro'];
                foreach ($qualities as $quality) {
                    $selected = ($movie['quality'] == $quality) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($quality) . "\" $selected>" . 
                         htmlspecialchars($quality) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div>
            <label for="backup">Backup (URL):</label>
            <input type="text" id="backup" name="backup" value="<?php echo htmlspecialchars($movie['backup'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="server">¿En servidor?:</label>
            <select id="server" name="server" required>
                <option value="si" <?php echo ($movie['server'] == 'si') ? 'selected' : ''; ?>>Sí</option>
                <option value="no" <?php echo ($movie['server'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        
        <div>
            <label for="rating">Calificación (1-10):</label>
            <input type="number" id="rating" name="rating" min="1" max="10" value="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?>">
        </div>
        
        <div>
            <label>Póster actual:</label>
            <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>" alt="Póster actual" width="200">
        </div>
        
        <div>
            <label for="poster">Cambiar póster (opcional):</label>
            <input type="file" id="poster" name="poster" accept="image/*">
        </div>
        
        <div>
            <button type="submit">Actualizar Película</button>
            <a href="show_movie.php?id=<?php echo $id; ?>">Cancelar</a>
        </div>
    </form>
</body>
</html>