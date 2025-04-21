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

// Crear instancia del controlador
$controller = new SerieController();

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una serie para editar.');
}

$id = $_GET['id'];

// Obtener los datos actuales de la serie
$serie = $controller->getSerie($id);

// Llamar al controlador de usuarios
$userController = new UserController();

// Si la serie no existe, mostrar error
if (!$serie) {
    die('Error: La serie solicitada no existe.');
}

// Verificar que la serie pertenece al usuario actual
$userId = $userController->getUserIdByUsername($_SESSION['username']);
$isOwner = $controller->checkSerieBelongsToUser($id, $userId);

// Si intenta borrar una película que no le "pertenece", redirigir
if (!$isOwner) {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Llamar al método del controlador para actualizar la serie
        $controller->updateSerie($id);
        
        // Redirigir a la vista concreta
        header("Location: show_serie.php?id=$id");
    } catch (Exception $e) {
        echo("Error al actualizar la serie: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serie</title>
</head>
<body>
    <h1>Editar Serie: <?php echo htmlspecialchars($serie['name']); ?></h1>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($serie['name']); ?>" required>
        </div>
        
        <div>
            <label for="gender">Género:</label>
            <select id="gender" name="gender" required>
                <?php
                $genders = ['acción/aventura', 'animación', 'anime', 'ciencia ficción', 'cortometraje', 
                           'comedia', 'deportes', 'documental', 'drama', 'familiar', 'fantasía', 
                           'guerra', 'terror', 'musical', 'suspense', 'romance', 'vaqueros', 'misterio'];
                foreach ($genders as $gender) {
                    $selected = ($serie['gender'] == $gender) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($gender) . "\" $selected>" . 
                         ucfirst(htmlspecialchars($gender)) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div>
            <label for="languages">Idiomas:</label>
            <input type="text" id="languages" name="languages" value="<?php echo htmlspecialchars($serie['languages']); ?>" required>
        </div>
        
        <div>
            <label for="seasons">Temporadas:</label>
            <input type="number" id="seasons" name="seasons" min="1" value="<?php echo htmlspecialchars($serie['seasons']); ?>" required>
        </div>
        
        <div>
            <label for="complete">¿Completa?:</label>
            <select id="complete" name="complete" required>
                <option value="si" <?php echo ($serie['complete'] == 'si') ? 'selected' : ''; ?>>Sí</option>
                <option value="no" <?php echo ($serie['complete'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        
        <div>
            <label for="year">Año:</label>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($serie['year']); ?>" required>
        </div>
        
        <div>
            <label for="quality">Calidad:</label>
            <select id="quality" name="quality" required>
                <?php
                $qualities = ['4K', '1440p', '1080p', '720p', '420p', 'otro'];
                foreach ($qualities as $quality) {
                    $selected = ($serie['quality'] == $quality) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($quality) . "\" $selected>" . 
                         htmlspecialchars($quality) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div>
            <label for="backup">Backup (URL):</label>
            <input type="text" id="backup" name="backup" value="<?php echo htmlspecialchars($serie['backup'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="size">Tamaño (MB):</label>
            <input type="number" id="size" name="size" step="0.01" value="<?php echo htmlspecialchars($serie['size']); ?>" required>
        </div>
        
        <div>
            <label for="server">¿En servidor?:</label>
            <select id="server" name="server" required>
                <option value="si" <?php echo ($serie['server'] == 'si') ? 'selected' : ''; ?>>Sí</option>
                <option value="no" <?php echo ($serie['server'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        
        <div>
            <label for="rating">Calificación (1-10):</label>
            <input type="number" id="rating" name="rating" min="1" max="10" value="<?php echo htmlspecialchars($serie['rating'] ?? ''); ?>">
        </div>
        
        <div>
            <label>Póster actual:</label>
            <img src="../../<?php echo htmlspecialchars($serie['poster']); ?>" alt="Póster actual" width="200">
        </div>
        
        <div>
            <label for="poster">Cambiar póster (opcional):</label>
            <input type="file" id="poster" name="poster" accept="image/*">
        </div>
        
        <div>
            <button type="submit">Actualizar Serie</button>
            <a href="show_serie.php?id=<?php echo $id; ?>">Cancelar</a>
        </div>
    </form>
</body>
</html>