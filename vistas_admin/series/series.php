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

// Crear instancia del controlador de usuarios
$userController = new UserController();

$username = $_SESSION['username'];
$userRol = $userController->getUserRol($username);

// Comprobar que el usuario sea 'root'
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

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
$controller = new SerieController();

// Obtener las series vinculadas al usuario actual
$series = $controller->index();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series</title>
    <!--Enlace al CSS de bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../css/series/series.css" type="text/css" rel="stylesheet" />

    <!--Para iconos-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/serie.png" type="image/x-icon">
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

                        <!--Barra de búsqueda-->
                        <li class="nav-item me-1">
                            <div class="input-group">
                                <input type="search" id="buscador" placeholder="Busca una serie..."
                                    class="form-control" />
                                <button type="button" class="btn btn-light" data-mdb-ripple-init>
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </li>

                        <!--Mostrar solo las series completas-->
                        <li class="nav-item">
                            <div>
                                <input hidden type="checkbox" onchange="showCompleteSeries()" id="completeSeries" />
                                <label class="form-check-label nav-link" style="cursor: pointer;" for="completeSeries">
                                    Mostrar series completas
                                </label>
                            </div>
                        </li>
                    </ul>
                    <span class="navbar-text text-light">
                        Series
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container-fluid px-4 mb-5 mt-2">
            <div
                class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-4 g-4 justify-content-center">
                <!-- Comprobar que hay al menos una serie -->
                <?php if (empty($series)): ?>
                    <div class="col-12 text-center mt-5">
                        <p class="lead">No hay series disponibles.</p>
                        <a href="add_serie.php" class="btn btn-primary mt-3">Añadir Nueva Serie</a>
                    </div>
                <?php else: ?>
                    <!-- Mostrar series en tarjetas -->
                    <?php foreach ($series as $serie): ?>
                        <div class="col">
                            <div class="card serie">
                                <div class="card-img-container">
                                    <img src="../../<?php echo htmlspecialchars($serie['poster']); ?>"
                                        alt="<?php echo htmlspecialchars($serie['name']); ?>" />
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title" title="<?php echo htmlspecialchars($serie['name']); ?>">
                                        <?php echo htmlspecialchars($serie['name']); ?>
                                    </h5>
                                    <p class="d-none complete"><?php echo htmlspecialchars($serie['complete']); ?></p>
                                    <a href="./show_serie.php?id=<?php echo urlencode($serie['id_serie']); ?>"
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
                            <a class="nav-link" href="../usuarios/users.php">Volver atrás</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../../vistas/usuarios/logout.php">
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
    <script src="../../js/series/search_series.js"></script>

    <!-- Enlace al archivo JavaScript de solo mostrar series completas -->
    <script src="../../js/series/complete_series.js"></script>
</body>

</html>