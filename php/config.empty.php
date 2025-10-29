<?php
$host = "";
$dbname = "";
$username = "";
$password = "";

// Definir la ruta base del proyecto
$projectRoot = dirname(__DIR__); // Raíz del proyecto
$documentRoot = $_SERVER['DOCUMENT_ROOT']; // Raíz del servidor web
define('PROJECT_PATH', str_replace($documentRoot, '', $projectRoot));

try {
    // Intentar conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla la conexión a la BD, comprobar si se encuentra en la página de instalación
    $currentPage = basename($_SERVER['PHP_SELF']);

    if ($currentPage !== 'install.php') {
        // No se encuentra en instalación, redirigir
        header('Location: ' . PROJECT_PATH . '/vistas/instalacion/install.php');
        exit();
    }

    // Si se encuentra en la página de instalación, conectar sin especificar BD
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e2) {
        die("Error crítico en la conexión: " . $e2->getMessage());
    }
}
?>