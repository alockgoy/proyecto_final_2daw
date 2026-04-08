<?php
require_once '../../php/backup/BackupController.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/movimientos/MovementController.php';

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

// Obtener el ID del usuario
$userController = new UserController();
$userId = $userController->getUserIdByUsername($_SESSION['username']);

if (!$userId) {
    session_destroy();
    header("Location: ../../index.html");
    exit();
}

// Registrar movimiento
$movementController = new MovementController();
$movementController->addMovement(
    $_SESSION['username'], 
    "ha exportado una copia de seguridad completa", 
    date('Y-m-d H:i:s'), 
    "correcto"
);

// Crear instancia del controlador de backup
$backupController = new BackupController();

// Exportar backup
$backupController->exportBackup($userId);
?>