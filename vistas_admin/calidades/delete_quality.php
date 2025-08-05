<?php
require_once '../../php/calidades/QualityController.php';
require_once '../../php/calidades/Quality.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';
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

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una calidad para eliminar.');
}

$qualityId = $_GET['id'];

// Crear instancias de los controladores
$controller = new QualityController();
$userController = new UserController();
$movementController = new MovementController();

// Comprobar que el usuario sea 'root'
$userRol = $userController->getUserRol($_SESSION['username']);
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

try {
    if ($controller->checkMovieQuality($qualityId)) {
        header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        exit();
    } elseif ($controller->checkSerieQuality($qualityId)) {
        header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        exit();
    }

    // Eliminar la calidad si todas las verificaciones son correctas
    $quality = $controller->getQualityById($qualityId);
    $qualityName = $quality ? $quality['name'] : 'calidad desconocida';
    $movementController->addMovement($_SESSION['username'], "ha eliminado la calidad $qualityName", date('Y-m-d H:i:s'), "correcto");
    $controller->deletequality($qualityId);

    // Redirigir a la lista de películas
    header('Location: qualities.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>