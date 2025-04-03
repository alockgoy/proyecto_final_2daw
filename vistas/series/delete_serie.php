<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una serie para eliminar.');
}

// Crear instancia del controlador
$controller = new SerieController();

$id = $_GET['id'];

try {
    // Llamar al método para eliminar la serie
    $result = $controller->deleteSerie($id);
    
    // Redirigir a la lista de series
    header('Location: series.php');
    exit;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>