<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/EpisodioController.php';
require_once __DIR__ . '/Episodio.php';

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$serie_id  = intval($_POST['serie_id']  ?? 0);
$temporada = intval($_POST['temporada'] ?? 0);

if (!$serie_id || !$temporada) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

$controller = new EpisodioController();
$result = $controller->deleteTemporada($serie_id, $temporada);

echo json_encode(['success' => $result]);
