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

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una película para eliminar.');
}

$movieId = $_GET['id'];

// Crear instancias de los controladores
$controller = new MovieController();
$userController = new UserController();

// Comprobar que el usuario sea 'root'
$userRol = $userController->getUserRol($_SESSION['username']);
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

try {
    // Eliminar la película si todas las verificaciones son correctas
    $controller->deleteMovie($movieId);

    // Redirigir a la lista de películas
    header('Location: movies.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>