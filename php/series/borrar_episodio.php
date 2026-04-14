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

$id_episodio = intval($_POST['id_episodio'] ?? 0);
if (!$id_episodio) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$controller = new EpisodioController();
$result = $controller->deleteEpisodio($id_episodio);

echo json_encode(['success' => $result, 'message' => $controller->lastError]);
