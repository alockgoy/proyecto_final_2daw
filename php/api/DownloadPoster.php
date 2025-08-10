<?php
require_once '../peliculas/MovieController.php';

header('Content-Type: application/json');

if (!isset($_GET['url']) || !isset($_GET['name'])) {
    echo json_encode(['success' => false, 'error' => 'Parámetros faltantes']);
    exit;
}

try {
    $posterUrl = $_GET['url'];
    $movieName = $_GET['name'];
    
    // Limpiar el nombre de la película para usarlo como nombre de archivo
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $movieName);
    $safeName = substr($safeName, 0, 50); // Limitar longitud
    
    // Obtener la extensión de la imagen
    $imageInfo = getimagesize($posterUrl);
    $extension = image_type_to_extension($imageInfo[2], false);
    
    $filename = $safeName . '_poster.' . $extension;
    $uploadDir = '../../img/posters/';
    
    // Crear directorio si no existe
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filePath = $uploadDir . $filename;
    
    // Descargar la imagen
    $imageData = file_get_contents($posterUrl);
    
    if ($imageData !== false) {
        $result = file_put_contents($filePath, $imageData);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true, 
                'filename' => $filename,
                'filepath' => 'img/posters/' . $filename
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo descargar la imagen']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>