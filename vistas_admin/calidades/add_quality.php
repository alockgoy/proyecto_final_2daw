<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';
require_once '../../php/calidades/QualityController.php';
require_once '../../php/calidades/Quality.php';
require_once '../../php/movimientos/MovementController.php';
require_once '../../php/movimientos/Movement.php';

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

$error = "";

// Llamar al controlador de usuarios
$userController = new UserController();

// Llamar al controlador de calidades
$qualityController = new QualityController();

// Llamar al controlador de movimientos
$movementController = new MovementController();

$username = $_SESSION['username'];
$userRol = $userController->getUserRol($username);

// Comprobar que el usuario sea 'root'
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Comprobar que el nombre no esté vacío
        $qualityName = trim($_POST["name"]);

        if (empty($qualityName)) {
            $movementController->addMovement($username, "ha intentado añadir una calidad vacía", date('Y-m-d H:i:s'), "fallido");
            $error = "El nombre de la calidad no puede estar vacío.";
        } else {
            // Intentar añadir la calidad
            if ($qualityController->addQuality($qualityName)) {
                $movementController->addMovement($username, "ha añadido la calidad: $qualityName", date('Y-m-d H:i:s'), "correcto");
                $success = "Calidad añadida correctamente, redirigiendo...";
            } else {
                $movementController->addMovement($username, "ha intentado añadir la calidad: $qualityName", date('Y-m-d H:i:s'), "fallido");
                $error = "Error al añadir la calidad. Puede que ya exista.";
            }
        }
    } catch (Exception $e) {
        $movementController->addMovement($username, "ha intentado añadir una calidad", date('Y-m-d H:i:s'), "error");
        $error = "Error al añadir la calidad: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../../css/series/edit_serie.css" type="text/css" rel="stylesheet" />
    <!-- Enlace al CSS de bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../img/iconos_navegador/serie.png" type="image/x-icon" />

    <title>Añadir calidad</title>
</head>

<body class="bg-light">
    <div class="container form-container py-4">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0"><i class="fas fa-edit me-2"></i>Añadir calidad: </h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert" data-redirect="./qualities.php">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3 text-center align-items-center">
                        <!-- Nombre de la calidad -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-cog"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="name" name="name" value=""
                                        placeholder="Ej: 1080p" required />
                                    <label for="name">Nombre de la calidad</label>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <a href="./qualities.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Volver atrás<samp></samp>
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Añadir calidad
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
    <script src="../../js/calidades/add_quality.js"></script>

</body>

</html>