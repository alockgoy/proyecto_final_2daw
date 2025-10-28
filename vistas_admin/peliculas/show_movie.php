<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';
require_once '../../php/calidades/QualityController.php';
require_once '../../php/calidades/Quality.php';

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

// Llamar al controlador de usuarios
$userController = new UserController();

// Llamar al controlador de calidades
$qualityController = new QualityController();

// Comprobar que el usuario sea 'root'
$userRol = $userController->getUserRol($_SESSION['username']);
if ($userRol != "root" && $userRol != "propietario") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

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

// Obtener la calidad de la película
$qualityName = $qualityController->getQualityById($movie['id_quality']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/peliculas/show_movie.css" type="text/css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon">
    <!-- Título de la película seleccionada -->
    <title><?php echo htmlspecialchars($movie['name']); ?></title>
</head>

<body>

    <div class="container mt-4">
        <div class="row">

            <!-- Poster de la película -->
            <aside class="col-md-4">
                <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>"
                    alt="<?php echo htmlspecialchars($movie['name']); ?>" class="img-fluid rounded shadow">
            </aside>

            <!-- Campos con los detalles -->
            <main class="col-md-8">

                <!-- Título de la película -->
                <h2><?php echo htmlspecialchars($movie['name']); ?></h2>

                <!-- Resto de detalles -->
                <ul class="list-group mb-4">

                    <!-- Director -->
                    <li class="list-group-item"><strong>Director:</strong>
                        <?php echo htmlspecialchars($movie['director']); ?></li>

                    <!-- Género -->
                    <li class="list-group-item"><strong>Género(s):</strong>
                        <?php echo htmlspecialchars($movie['gender']); ?></li>

                    <!-- Año -->
                    <li class="list-group-item"><strong>Año:</strong> <?php echo htmlspecialchars($movie['year']); ?>
                    </li>

                    <!-- Idioma(s) -->
                    <li class="list-group-item"><strong>Idioma(s):</strong>
                        <?php echo htmlspecialchars($movie['languages']); ?></li>

                    <!-- Calidad -->
                    <li class="list-group-item"><strong>Calidad:</strong>
                        <?php echo htmlspecialchars($qualityName['name']); ?></li>

                    <!-- Tamaño en GB -->
                    <li class="list-group-item"><strong>Tamaño:</strong> <?php echo htmlspecialchars($movie['size']); ?>
                        GB</li>

                    <!-- Puntuación -->
                    <?php if (!empty($movie['rating'])): ?>
                        <li class="list-group-item">
                            <strong>Valoración:</strong>
                            <span>
                                <?php echo htmlspecialchars($movie['rating']); ?>/10
                            </span>
                        </li>
                    <?php endif; ?>


                    <!-- ¿Copia de seguridad? -->
                    <?php if (!empty($movie['backup'])): ?>
                        <li class="list-group-item"><strong>Backup:</strong>
                            <?php echo htmlspecialchars($movie['backup']); ?></li>
                    <?php endif; ?>

                    <!-- ¿Está en un servidor multimedia? -->
                    <li class="list-group-item"><strong>En servidor:</strong>
                        <?php echo $movie['server'] == 'si' ? 'Sí' : 'No'; ?></li>
                </ul>

                <!-- Sinopsis -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="card-title h5 mb-0">Sinopsis</h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($movie['synopsis']); ?></p>
                    </div>
                </div>

                <!-- Botón para ir a la pantalla de edición -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <a href="edit_movie.php?id=<?php echo $movie['id_movie']; ?>" class="btn btn-warning me-md-2">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                </div>
            </main>
        </div>
    </div>

    <!-- Botones de borrar película y volver atrás -->
    <footer class="bg-dark text-white text-center py-3 fixed-bottom">
        <div class="container footer-container d-flex justify-content-center align-items-center">
            <a href="#" data-confirm="¿Estás seguro de que deseas eliminar esta película?"
                data-url="delete_movie.php?id=<?php echo $movie['id_movie']; ?>" data-confirm-text="Borrar Película"
                class="btn btn-danger me-2">Borrar Película</a>
            <a href="movies.php" class="btn btn-secondary">Volver Atrás</a>
        </div>
    </footer>

    <!-- Enlace al archivo JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />

    <!-- Enlace al JS del modal de bootstrap -->
    <script src="../../js/confirm_modal.js"></script>
</body>

</html>