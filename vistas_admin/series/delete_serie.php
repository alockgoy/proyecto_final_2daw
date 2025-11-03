<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';
require_once '../../php/movimientos/MovementController.php';
require_once '../../php/movimientos/Movement.php';
require_once '../../php/seguridad.php';

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}


// Crear instancias de los controladores
$controller = new SerieController();
$userController = new UserController();
$movementController = new MovementController();

$username = $_SESSION['username'];
$userRol = $userController->getUserRol($username);

// Comprobar que el usuario sea 'root'
if ($userRol != "root" && $userRol != "propietario") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Error: Método no permitido');
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header('Location: series.php?error=csrf');
    exit();
}
regenerarTokenCSRF();

// Verificar que se ha proporcionado un ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    die('Error: No se ha especificado una serie para eliminar.');
}

$id = $_POST['id'];

try {
    // Eliminar la serie si todas las verificaciones son correctas
    $serieData = $controller->getSerie($id);
    $serieName = $serieData ? $serieData['name'] : 'serie desconocida';
    $movementController->addMovement($_SESSION['username'], "ha eliminado la serie $serieName", date('Y-m-d H:i:s'), "correcto");
    $controller->deleteSerie($id);

    // Redirigir a la lista de series
    header('Location: series.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>