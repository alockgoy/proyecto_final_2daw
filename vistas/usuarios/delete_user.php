<?php
// Importar lo necesario
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Traer los archivos de usuarios
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
    die('Error: No se ha especificado un usuario para eliminar.');
}

$userId = $_GET['id'];

// Obtener datos del usuario actual
$username = $_SESSION['username'];

// Crear instancias de los controladores
$userController = new UserController();

try {
    // Obtener el ID del usuario actual
    $userId = $userController->getUserIdByUsername($_SESSION['username']);
    
    // Verificar que el ID del usuario coincide con el usuario que ha iniciado sesión
    $isUser = $userController->getUserIdByUsername($username);
    
    // Si se intenta borrar un usuario que no ha iniciado sesión, redirigir
    if (!$isUser) {
        header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        exit();
    }
    
    // Eliminar el usuario si todas las verificaciones son correctas
    $userController->deleteUser($userId);
    
    // Redirigir a la lista de películas
    header('Location: movies.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

?>