<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una película para eliminar.');
}

// Crear instancia del controlador
$controller = new MovieController();

$id = $_GET['id'];

try {
    // Llamar al método para eliminar la película
    $result = $controller->deleteMovie($id);
    
    // Redirigir a la lista de películas con un mensaje de éxito
    header('Location: movies.php?deleted=true');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>