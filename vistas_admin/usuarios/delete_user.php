<?php
// Traer los archivos de usuarios
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Traer los archivos de películas
require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';

// Traer los archivos de series
require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';

// Traer los archivos de movimientos
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
$userController = new UserController();
$movieController = new MovieController();
$serieController = new SerieController();
$movementController = new MovementController();

$adminUsername = $_SESSION['username'];
$userRol = $userController->getUserRol($adminUsername);

// Comprobar que el usuario sea 'root'
if ($userRol != "root" && $userRol != "propietario") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Error: Método no permitido');
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header('Location: users.php?error=csrf');
    exit();
}
regenerarTokenCSRF();

// Verificar que se ha proporcionado un ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    die('Error: No se ha especificado un usuario para eliminar.');
}

try {
    // Obtener el ID del usuario actual
    $userId = $_POST['id'];
    
    // Eliminar el usuario si todas las verificaciones son correctas
    $user = $userController->getUserById($userId);
    $username = $user ? $user['username'] : 'Desconocido';
    $movementController->addMovement($adminUsername, "ha eliminado a $username", date('Y-m-d H:i:s'), "correcto");
    $userController->deleteUser($userId);

    // Eliminar toda la multimedia que no tenga usuario asociado
    $movieController->deleteMoviesWithoutUsers();
    $serieController->deleteSeriesWithoutUsers();
    
    // Redirigir
    header('Location: ./users.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die('Error: ' . $e->getMessage());
}

?>