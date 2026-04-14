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
$id_quality  = intval($_POST['id_quality']  ?? 0);
$size        = floatval($_POST['size']       ?? 0);
$backup      = trim($_POST['backup']         ?? '');
$server      = trim($_POST['server']         ?? '');

if (!$id_episodio || !$id_quality || $size <= 0 || !in_array($server, ['si', 'no'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

$controller = new EpisodioController();
$result = $controller->updateEpisodio($id_episodio, $id_quality, $size, $backup ?: null, $server);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $controller->lastError ?: 'Error al actualizar']);
}
