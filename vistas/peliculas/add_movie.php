<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

$controller = new MovieController();
$userController = new UserController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Añadir la película
        if ($controller->addMovie()) {
            // Obtener el ID de la última película insertada
            $movieId = $controller->getLastInsertedId();
            
            // Obtener el ID del usuario actual
            $userId = $userController->getUserIdByUsername($_SESSION['username']);

            // Asociar la película con el usuario
            $result = $controller->associateMovieWithUser($movieId, $userId);
            
            if ($result) {
                header("Location: movies.php");
                exit();
            } else {
                throw new Exception("Error al asociar la película con el usuario.");
            }
        } else {
            throw new Exception("Error al añadir la película.");
        }
    } catch (Exception $e) {
        echo("Error al añadir la película: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Película</title>
</head>
<body>
    <h1>Añadir Película</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nombre" required /> <br/>
        <textarea name="synopsis" placeholder="Sinopsis"></textarea> <br/>
        <input type="file" name="poster" accept="image/*" required /> <br/>
        <input type="text" name="director" placeholder="Director" required /> <br/>
        <select name="gender" required>
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
        </select><br>
        <input type="text" name="languages" placeholder="Idiomas" required /> <br/>
        <input type="number" name="size" placeholder="Tamaño (MB)" step="0.01" required /> <br/>
        <input type="number" name="year" placeholder="Año" required /> <br/>
        <select name="quality" required>
            <option value="">Selecciona la calidad</option>
            <option value="4K">4K</option>
            <option value="1440p">1440p</option>
            <option value="1080p">1080p</option>
            <option value="720p">720p</option>
            <option value="420p">420p</option>
            <option value="otro">Otro</option>
        </select><br>
        <input type="text" name="backup" placeholder="Backup (URL)" /> <br/>
        <select name="server" required>
            <option value="">¿En servidor?</option>
            <option value="si">Sí</option>
            <option value="no">No</option>
        </select><br>
        <input type="number" name="rating" placeholder="Calificación (1-10)" min="1" max="10" /> <br/>
        <button type="submit">Guardar</button>
    </form>
    <a href="movies.php">Volver</a>
</body>
</html>