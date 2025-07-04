<?php
// Importar lo necesario
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Traer los archivos de usuarios
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Traer los archivos de películas
require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';

// Traer los archivos de series
require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';

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
$movieController = new MovieController();
$serieController = new SerieController();

try {
    // Obtener el ID del usuario actual
    $userId = $_GET['id'];
    
    // Verificar que el ID del usuario coincide con el usuario que ha iniciado sesión
    $isUser = $userController->getUserIdByUsername($username);
    
    // Si se intenta borrar un usuario que no ha iniciado sesión, redirigir
    if ($userId != $isUser) {
        header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        exit();
    }
    
    // Eliminar el usuario si todas las verificaciones son correctas
    $userController->deleteUser($userId);

    // Eliminar toda la multimedia que no tenga usuario asociado
    $movieController->deleteMoviesWithoutUsers();
    $serieController->deleteSeriesWithoutUsers();

    // Cerrar la sesión
    session_destroy();
    
    // Redirigir a la lista de películas
    header('Location: ../../index.html');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die('Error: ' . $e->getMessage());
}

?>