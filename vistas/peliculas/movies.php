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

// Obtener la ruta de la foto de perfil del usuario
$profilePicture = $userController->getUserProfilePicture($_SESSION['username']);

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

    <!--Para iconos-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon">
</head>

<body>
    <header>
        <!--Barra de navegación-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid px-2">
                <!-- Foto de perfil del usuario -->
                <a class="navbar-brand" href="../usuarios/my_profile.php">
                    <img src="<?php echo !empty($profilePicture) ? '../../' . htmlspecialchars($profilePicture) : '../../img/avatares_usuarios/default.jpg'; ?>"
                        width="50" height="50" class="rounded-circle" alt="Foto de perfil">
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

                        <li class="nav-item me-2">
                            <a class="nav-link" href="../series/series.php">Ir a series</a>
                        </li>

                        <!-- Botón para alternar -->
                        <li class="nav-item me-3 d-flex align-items-center">
                            <button class="btn btn-outline-light btn-sm" id="toggle-search">
                                Buscar por director
                            </button>
                        </li>

                        <!--Barra de búsqueda-->
                        <li class="nav-item" id="search-by-name">
                            <div class="input-group">
                                <input type="search" id="buscador" placeholder="Busca una película..."
                                    class="form-control" />
                                <button type="button" class="btn btn-light">
                                    <i class="fa-solid fa-film"></i>
                                </button>
                            </div>
                        </li>

                        <!--Barra de búsqueda de directores-->
                        <li class="nav-item d-none" id="search-by-director">
                            <div class="input-group">
                                <input type="search" id="buscador_directores" placeholder="Busca por un director..."
                                    class="form-control" />
                                <button type="button" class="btn btn-light" data-mdb-ripple-init>
                                    <i class="fa-solid fa-user-tie"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                    <span class="navbar-text text-light">
                        Películas de <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container-fluid px-4 mb-5 mt-2">
            <div
                class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-4 g-4 justify-content-center">
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
                            <div class="card pelicula">
                                <div class="card-img-container">
                                    <img src="../../<?php echo htmlspecialchars($movie['poster']); ?>"
                                        alt="<?php echo htmlspecialchars($movie['name']); ?>" />
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title" title="<?php echo htmlspecialchars($movie['name']); ?>">
                                        <?php echo htmlspecialchars($movie['name']); ?>
                                    </h5>
                                    <p class="d-none director"><?php echo htmlspecialchars($movie['director']); ?></p>
                                    <a href="./show_movie.php?id=<?php echo urlencode($movie['id_movie']); ?>"
                                        class="btn btn-primary mt-auto">Detalles</a>
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
            <div class="container-fluid px-2">
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
                            <a class="nav-link" href="../usuarios/logout.php">
                                <i class="fa-solid fa-right-from-bracket me-1"></i>
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </footer>

    <!-- Enlace al archivo JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al archivo JavaScript del buscador -->
    <script src="../../js/search_movies.js"></script>

    <!-- Enlace al archivo JavaScript del buscador de directores -->
    <script src="../../js/search_director.js"></script>

    <!-- Enlace al archivo JavaScript de alternar los buscadores -->
    <script src="../../js/alternate_search-bar.js"></script>
</body>

</html>