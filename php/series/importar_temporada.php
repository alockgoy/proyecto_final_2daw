<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/EpisodioController.php';
require_once __DIR__ . '/Episodio.php';
require_once __DIR__ . '/../seguridad.php';

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

$serie_id   = intval($_POST['serie_id']   ?? 0);
$temporada  = intval($_POST['temporada']  ?? 0);
$id_quality = intval($_POST['id_quality'] ?? 0);
$size       = floatval($_POST['size']     ?? 0);
$backup     = trim($_POST['backup']       ?? '');
$server     = trim($_POST['server']       ?? '');
$episodios  = json_decode($_POST['episodios'] ?? '[]', true);

if (!$serie_id || !$temporada || !$id_quality || $size <= 0 || !in_array($server, ['si', 'no']) || empty($episodios)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos']);
    exit();
}

$controller = new EpisodioController();
$result = $controller->importarTemporada($serie_id, $temporada, $episodios, $id_quality, $size, $backup ?: null, $server);

echo json_encode([
    'success'   => true,
    'importados' => $result['importados'],
    'omitidos'   => $result['omitidos'],
    'errores'    => $result['errores']
]);
