<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';
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
$controller = new SerieController();

// Llamar al controlador de usuarios
$userController = new UserController();

// Llamar al controlador de calidades
$qualityController = new QualityController();

$username = $_SESSION['username'];
$userRol = $userController->getUserRol($username);

// Comprobar que el usuario sea 'root'
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

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

// Obtener la calidad de la serie
$qualityName = $qualityController->getQualityById($serie['id_quality']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/series/show_serie.css" type="text/css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon">
    <!-- Título de la serie seleccionada -->
    <title><?php echo htmlspecialchars($serie['name']); ?></title>
</head>

<body>

    <div class="container mt-4">
        <div class="row">

            <!-- Poster de la serie -->
            <aside class="col-md-4">
                <img src="../../<?php echo htmlspecialchars($serie['poster']); ?>"
                    alt="<?php echo htmlspecialchars($serie['name']); ?>" class="img-fluid rounded shadow">
            </aside>

            <!-- Campos con los detalles -->
            <main class="col-md-8">

                <!-- Título de la serie -->
                <h2><?php echo htmlspecialchars($serie['name']); ?></h2>

                <!-- Resto de detalles -->
                <ul class="list-group mb-4">

                    <!-- Género -->
                    <li class="list-group-item"><strong>Género:</strong>
                        <?php echo htmlspecialchars($serie['gender']); ?></li>

                    <!-- Año -->
                    <li class="list-group-item"><strong>Año:</strong> <?php echo htmlspecialchars($serie['year']); ?>
                    </li>

                    <!-- Idioma(s) -->
                    <li class="list-group-item"><strong>Idioma(s):</strong>
                        <?php echo htmlspecialchars($serie['languages']); ?></li>

                    <!-- Temporadas -->
                    <li class="list-group-item"><strong>Temporadas:</strong>
                        <?php echo htmlspecialchars($serie['seasons']); ?></li>

                    <!-- ¿Completa? -->
                    <li class="list-group-item"><strong>Completa:</strong>
                        <?php echo $serie['complete'] == 'si' ? 'Sí' : 'No'; ?></li>

                    <!-- Calidad -->
                    <li class="list-group-item"><strong>Calidad:</strong>
                        <?php echo htmlspecialchars($qualityName['name']); ?></li>

                    <!-- Tamaño en GB -->
                    <li class="list-group-item"><strong>Tamaño:</strong> <?php echo htmlspecialchars($serie['size']); ?>
                        GB</li>

                    <!-- Puntuación -->
                    <?php if (!empty($serie['rating'])): ?>
                        <li class="list-group-item">
                            <strong>Valoración:</strong>
                            <span>
                                <?php echo htmlspecialchars($serie['rating']); ?>/10
                            </span>
                        </li>
                    <?php endif; ?>


                    <!-- ¿Copia de seguridad? -->
                    <?php if (!empty($serie['backup'])): ?>
                        <li class="list-group-item"><strong>Backup:</strong>
                            <?php echo htmlspecialchars($serie['backup']); ?></li>
                    <?php endif; ?>

                    <!-- ¿Está en un servidor multimedia? -->
                    <li class="list-group-item"><strong>En servidor:</strong>
                        <?php echo $serie['server'] == 'si' ? 'Sí' : 'No'; ?></li>
                </ul>

                <!-- Sinopsis -->
                <?php if (isset($serie['synopsis']) && !empty($serie['synopsis'])): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title h5 mb-0">Sinopsis</h3>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($serie['synopsis']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Botón para ir a la pantalla de edición -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <a href="edit_serie.php?id=<?php echo $serie['id_serie']; ?>" class="btn btn-warning me-md-2">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                </div>
            </main>
        </div>
    </div>

    <!-- Botones de borrar serie y volver atrás -->
    <footer class="bg-dark text-white text-center py-3 fixed-bottom">
        <div class="container footer-container d-flex justify-content-center align-items-center">
            <a href="#" data-confirm="¿Estás seguro de que deseas eliminar esta serie?"
                data-url="delete_serie.php?id=<?php echo $serie['id_serie']; ?>" data-confirm-text="Borrar Serie"
                class="btn btn-danger me-2">Borrar Serie</a>
            <a href="series.php" class="btn btn-secondary">Volver Atrás</a>
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