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
    <title>Películas</title>
    <!--Enlace al CSS de bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../css/movies.css" type="text/css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon">
</head>

<body>
    <header>
        <!--Barra de navegación-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <!-- Foto de perfil del usuario -->
                <a class="navbar-brand" href="">
                    <img src="../../<?php echo htmlspecialchars($userController->getUserProfilePicture($_SESSION['username'])); ?>" alt="Perfil de Usuario" width="50">
                </a>

                <!--Botón para colapsar la barra en pantallas pequeñas-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--Elementos de la barra de navegación-->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="./add_movie.php">Añadir una película</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="../series/series.php">Ir a series</a>
                        </li>
                    </ul>
                    <span class="navbar-text text-light">
                        Biblioteca de <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container-fluid px-4 mb-5 mt-2">
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-4 justify-content-center">
                <!-- Comprobar que hay al menos una película -->
                <?php if (empty($movies)): ?>
                    <div class="col-12 text-center mt-5">
                        <p class="lead">No hay películas disponibles.</p>
                        <a href="add_movie.php" class="btn btn-primary mt-3">Añadir Nueva Película</a>
                    </div>
                <?php else: ?>
                    <!-- Mostrar películas en tarjetas -->
                    <?php foreach ($movies as $movie): ?>
                        <div class="col">
                            <div class="card">
                                <div class="card-img-container">
                                    <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['name']); ?>" />
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($movie['name']); ?></h5>
                                    <a href="./show_movie.php?id=<?php echo urlencode($movie['id_movie']); ?>" class="btn btn-primary mt-auto" target="_blank">Detalles</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="mt-5 fixed-bottom">
        <!--Barra de navegación-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <!--Botón para colapsar la barra en pantallas pequeñas-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFooter">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--Elementos de la barra de navegación-->
                <div class="collapse navbar-collapse" id="navbarFooter">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../usuarios/my_profile.php">Mi perfil</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../usuarios/logout.php">Cerrar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </footer>

    <!-- Enlace al archivo JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>