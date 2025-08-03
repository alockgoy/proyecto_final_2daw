<?php
require_once '../../php/movimientos/MovementController.php';
require_once '../../php/movimientos/Movement.php';
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

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado un movimiento para eliminar.');
}

$movementId = $_GET['id'];

// Crear instancias de los controladores
$controller = new MovementController();
$userController = new UserController();

// Comprobar que el usuario sea 'root'
$userRol = $userController->getUserRol($_SESSION['username']);
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

try {
    // Eliminar el movimiento
    $controller->deleteMovement($movementId);

    // Redirigir a la lista
    header('Location: movements.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>