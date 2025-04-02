<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';
$controller = new SerieController();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $controller->addSerie();
        header("Location: series.php?added=true");
        exit();
    } catch (Exception $e) {
        $error = "Error al añadir la serie: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir serie</title>
</head>
<body>
    <h1>Añadir Nueva Serie</h1>
    
    <?php if (!empty($error)): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div>
            <label for="poster">Póster:</label>
            <input type="file" id="poster" name="poster" accept="image/*" required>
        </div>
        
        <div>
            <label for="gender">Género:</label>
            <select id="gender" name="gender" required>
                <option value="">Selecciona un género</option>
                <option value="acción/aventura">Acción/Aventura</option>
                <option value="animación">Animación</option>
                <option value="anime">Anime</option>
                <option value="ciencia ficción">Ciencia Ficción</option>
                <option value="cortometraje">Cortometraje</option>
                <option value="comedia">Comedia</option>
                <option value="deportes">Deportes</option>
                <option value="documental">Documental</option>
                <option value="drama">Drama</option>
                <option value="familiar">Familiar</option>
                <option value="fantasía">Fantasía</option>
                <option value="guerra">Guerra</option>
                <option value="terror">Terror</option>
                <option value="musical">Musical</option>
                <option value="suspense">Suspense</option>
                <option value="romance">Romance</option>
                <option value="vaqueros">Vaqueros</option>
                <option value="misterio">Misterio</option>
            </select>
        </div>
        
        <div>
            <label for="languages">Idiomas:</label>
            <input type="text" id="languages" name="languages" required>
        </div>
        
        <div>
            <label for="seasons">Temporadas:</label>
            <input type="number" id="seasons" name="seasons" min="1" required>
        </div>
        
        <div>
            <label for="complete">¿Completa?:</label>
            <select id="complete" name="complete" required>
                <option value="">Selecciona</option>
                <option value="si">Sí</option>
                <option value="no">No</option>
            </select>
        </div>
        
        <div>
            <label for="year">Año:</label>
            <input type="number" id="year" name="year" required>
        </div>
        
        <div>
            <label for="quality">Calidad:</label>
            <select id="quality" name="quality" required>
                <option value="">Selecciona la calidad</option>
                <option value="4K">4K</option>
                <option value="1440p">1440p</option>
                <option value="1080p">1080p</option>
                <option value="720p">720p</option>
                <option value="420p">420p</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        
        <div>
            <label for="backup">Backup (URL):</label>
            <input type="text" id="backup" name="backup">
        </div>
        
        <div>
            <label for="size">Tamaño (MB):</label>
            <input type="number" id="size" name="size" step="0.01" required>
        </div>
        
        <div>
            <label for="server">¿En servidor?:</label>
            <select id="server" name="server" required>
                <option value="">Selecciona</option>
                <option value="si">Sí</option>
                <option value="no">No</option>
            </select>
        </div>
        
        <div>
            <label for="rating">Calificación (1-10):</label>
            <input type="number" id="rating" name="rating" min="1" max="10">
        </div>
        
        <div>
            <button type="submit">Guardar Serie</button>
            <a href="series.php">Cancelar</a>
        </div>
    </form>
</body>
</html>