<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode(['error' => 'URL no proporcionada']);
    exit();
}

$imageUrl = $_GET['url'];

// Validar que la URL sea de TMDb
if (strpos($imageUrl, 'image.tmdb.org') === false) {
    http_response_code(400);
    echo json_encode(['error' => 'URL no válida']);
    exit();
}

// Descargar la imagen
$imageData = @file_get_contents($imageUrl);

if ($imageData === false) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo descargar la imagen']);
    exit();
}

// Convertir a base64
$base64 = base64_encode($imageData);

echo json_encode([
    'success' => true,
    'data' => $base64,
    'mime' => 'image/jpeg'
]);
?>