<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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

// Llamar al controlador de usuarios
$userController = new UserController();

// Si la película no existe, mostrar error
if (!$movie) {
    die('Error: La película solicitada no existe.');
}

// Verificar que la película pertenece al usuario actual
$userId = $userController->getUserIdByUsername($_SESSION['username']);
$isOwner = $controller->checkMovieBelongsToUser($id, $userId);

// Si intenta borrar una película que no le "pertenece", redirigir
if (!$isOwner) {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Intentar actualizar la película
    try {

        if ($controller->updateMovie($id)) {
            $success = "Película modificada correctamente, redirigiendo...";
        } else {
            // Obtener el error de validación del controlador
            $error = $controller->lastError ?: "Error al modificar la película.";
        }

        // Redirigir a la vista concreta
        //header("Location: show_movie.php?id=$id");
        //exit;
    } catch (Exception $e) {
        $error = "Error al actualizar la película: " . $e->getMessage();
    }

}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../../css/edit_movie.css" type="text/css" rel="stylesheet" />
    <!-- Enlace al CSS de bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon" />

    <title>Editar Película - <?php echo htmlspecialchars($movie['name']); ?></title>
</head>

<body class="bg-light">
    <div class="container form-container py-4">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0"><i class="fas fa-edit me-2"></i>Editar Película:
                    <?php echo htmlspecialchars($movie['name']); ?>
                </h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert" data-redirect="./movies.php">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Nombre de la película -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-film"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?php echo htmlspecialchars($movie['name']); ?>" placeholder="Nombre"
                                        required />
                                    <label for="name">Nombre de la película</label>
                                </div>
                            </div>
                        </div>

                        <!-- Año -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="year" name="year"
                                        value="<?php echo htmlspecialchars($movie['year']); ?>" placeholder="Año"
                                        required />
                                    <label for="year">Año</label>
                                </div>
                            </div>
                        </div>

                        <!-- Director -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="director" name="director"
                                        value="<?php echo htmlspecialchars($movie['director']); ?>"
                                        placeholder="Director" required />
                                    <label for="director">Director</label>
                                </div>
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-theater-masks"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="gender" name="gender" required>
                                        <?php
                                        $genders = [
                                            'acción/aventura',
                                            'animación',
                                            'anime',
                                            'ciencia ficción',
                                            'cortometraje',
                                            'comedia',
                                            'deportes',
                                            'documental',
                                            'drama',
                                            'familiar',
                                            'fantasía',
                                            'guerra',
                                            'terror',
                                            'musical',
                                            'suspense',
                                            'romance',
                                            'vaqueros',
                                            'misterio'
                                        ];
                                        foreach ($genders as $gender) {
                                            $selected = ($movie['gender'] == $gender) ? 'selected' : '';
                                            echo "<option value=\"" . htmlspecialchars($gender) . "\" $selected>" .
                                                ucfirst(htmlspecialchars($gender)) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <label for="gender">Género</label>
                                </div>
                            </div>
                        </div>

                        <!-- Idiomas -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-language"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="languages" name="languages"
                                        value="<?php echo htmlspecialchars($movie['languages']); ?>"
                                        placeholder="Idiomas" required />
                                    <label for="languages">Idiomas (ej: Español, Inglés)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Calidad -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-photo-video"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="quality" name="quality" required>
                                        <?php
                                        $qualities = ['4K', '1440p', '1080p', '720p', '420p', 'otro'];
                                        foreach ($qualities as $quality) {
                                            $selected = ($movie['quality'] == $quality) ? 'selected' : '';
                                            echo "<option value=\"" . htmlspecialchars($quality) . "\" $selected>" .
                                                htmlspecialchars($quality) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <label for="quality">Calidad</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tamaño -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hdd"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="size" name="size"
                                        value="<?php echo htmlspecialchars($movie['size']); ?>" placeholder="Tamaño"
                                        step="0.1" required />
                                    <label for="size">Tamaño (GB)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Calificación -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="rating" name="rating"
                                        value="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?>"
                                        placeholder="Calificación" min="1" max="10" />
                                    <label for="rating">Calificación (1-10)</label>
                                </div>
                            </div>
                        </div>

                        <!-- En servidor -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-server"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="server" name="server" required>
                                        <option value="si" <?php echo ($movie['server'] == 'si') ? 'selected' : ''; ?>>Sí
                                        </option>
                                        <option value="no" <?php echo ($movie['server'] == 'no') ? 'selected' : ''; ?>>No
                                        </option>
                                    </select>
                                    <label for="server">¿En servidor?</label>
                                </div>
                            </div>
                        </div>

                        <!-- Backup -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="backup" name="backup"
                                        value="<?php echo htmlspecialchars($movie['backup'] ?? ''); ?>"
                                        placeholder="¿Dónde está la copia de seguridad?" />
                                    <label for="backup">Backup</label>
                                </div>
                            </div>
                        </div>

                        <!-- Póster actual -->
                        <div class="col-12 form-group">
                            <label class="form-label mb-2"><i class="fas fa-image me-2"></i>Póster actual:</label>
                            <div class="mb-3">
                                <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>"
                                    alt="Póster de <?php echo htmlspecialchars($movie['name']); ?>"
                                    class="poster-preview img-fluid" height="400" width="200" />
                            </div>
                            <label for="poster" class="form-label">Cambiar póster (opcional):</label>
                            <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                        </div>

                        <!-- Sinopsis -->
                        <div class="col-12 form-group">
                            <label for="synopsis" class="form-label"><i
                                    class="fas fa-align-left me-2"></i>Sinopsis</label>
                            <textarea class="form-control" id="synopsis" name="synopsis" rows="4"
                                placeholder="Escribe una breve sinopsis de la película..."><?php echo htmlspecialchars($movie['synopsis']); ?></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <a href="show_movie.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Película
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enlace al Javascript de bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al Javascript de editar películas -->
    <script src="../../js/edit_movie.js"></script>

</body>

</html>