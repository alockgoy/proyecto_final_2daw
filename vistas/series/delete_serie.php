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

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una serie para eliminar.');
}

// Crear instancias de los controladores
$controller = new SerieController();
$userController = new UserController();

$id = $_GET['id'];

try {
    // Obtener el ID del usuario actual
    $userId = $userController->getUserIdByUsername($_SESSION['username']);
    
    // Verificar que la serie pertenece al usuario
    $isOwner = $controller->checkSerieBelongsToUser($id, $userId);
    
    // Si intenta borrar una serie que no le "pertenece", redirigir
    if (!$isOwner) {
        header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        exit();
    }
    
    // Eliminar la serie si todas las verificaciones son correctas
    $controller->deleteSerie($id);
    
    // Redirigir a la lista de películas
    header('Location: series.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>