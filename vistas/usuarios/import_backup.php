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

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
    $backupController = new BackupController();
    $movementController = new MovementController();

    $result = $backupController->importBackup($userId, $_FILES['backup_file']);

    if ($result['success']) {
        $movementController->addMovement(
            $_SESSION['username'],
            "ha importado una copia de seguridad ({$result['imported_movies']} películas, {$result['imported_series']} series)",
            date('Y-m-d H:i:s'),
            "correcto"
        );

        $message = "Backup importado correctamente. ";
        $message .= "Películas importadas: {$result['imported_movies']}, ";
        $message .= "Series importadas: {$result['imported_series']}";

        if (!empty($result['errors'])) {
            $message .= "<br><strong>Advertencias:</strong><br>" . implode("<br>", $result['errors']);
        }

        $messageType = 'success';
    } else {
        $movementController->addMovement(
            $_SESSION['username'],
            "ha intentado importar una copia de seguridad",
            date('Y-m-d H:i:s'),
            "fallido"
        );

        $message = $result['message'] ?? 'Error al importar el backup';
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Backup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-upload me-2"></i>Importar Copia de Seguridad</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($messageType === 'success'): ?>
                            <script>
                                // Redirigir después de 5 segundos si la operación fue exitosa
                                let seconds = 5;
                                const alertDiv = document.querySelector('.alert-success');
                                const countdownSpan = document.createElement('span');
                                countdownSpan.innerHTML = `<br><small>Redirigiendo a Mi Perfil en <strong id="countdown">${seconds}</strong> segundos...</small>`;
                                alertDiv.appendChild(countdownSpan);

                                const interval = setInterval(() => {
                                    seconds--;
                                    document.getElementById('countdown').textContent = seconds;

                                    if (seconds <= 0) {
                                        clearInterval(interval);
                                        window.location.href = 'my_profile.php';
                                    }
                                }, 1000);
                            </script>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>El archivo debe ser un backup exportado previamente en formato JSON</li>
                                <li>Se importarán TODAS las películas y series del backup</li>
                                <li>Las películas y series se añadirán a tu biblioteca actual</li>
                                <li>Las imágenes de pósters se restaurarán automáticamente</li>
                                <li>Ten en cuenta, que lo que exista en la copia de seguridad se añadirá a tus datos ya
                                    existentes
                                    <ul>
                                        <li>Esto quiere decir, que si ya tienes en la web "Película A" y la tienes
                                            también en la copia de seguridad, aparecerá duplicada tantas veces restaures
                                            la copia de seguridad.</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <form method="POST" enctype="multipart/form-data" id="importForm">
                            <div class="mb-3">
                                <label for="backup_file" class="form-label">
                                    <i class="fas fa-file-import me-2"></i>Seleccionar archivo de backup (.json)
                                </label>
                                <input type="file" class="form-control" id="backup_file" name="backup_file"
                                    accept=".json" required>
                                <div class="form-text">Solo se aceptan archivos JSON de backup</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-upload me-2"></i>Importar Backup
                                </button>
                                <a href="my_profile.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver a Mi Perfil
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Modal de confirmación específico para importar backup
        document.getElementById('importForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevenir envío inmediato

            const file = document.getElementById('backup_file').files[0];

            if (!file) {
                // Si no hay archivo, mostrar validación HTML5
                this.reportValidity();
                return;
            }

            const form = this;

            // Crear el modal
            const modalHtml = `
            <div class="modal fade" id="importConfirmModal" tabindex="-1" aria-labelledby="importConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="importConfirmModalLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>Confirmación de Importación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>¿Estás seguro de que deseas importar este backup?</strong></p>
                            <p class="mb-2">Archivo: <code>${file.name}</code></p>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                Esto añadirá todas las películas y series del backup a tu biblioteca.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" id="confirmImportBtn">
                                <i class="fas fa-upload me-2"></i>Importar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            // Remover modal anterior si existe
            const existingModal = document.getElementById('importConfirmModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Añadir modal al body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('importConfirmModal'));
            modal.show();

            // Manejar confirmación
            document.getElementById('confirmImportBtn').addEventListener('click', function () {
                modal.hide();
                // Enviar el formulario real
                form.submit();
            });

            // Limpiar modal cuando se cierre
            document.getElementById('importConfirmModal').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        });
    </script>
</body>

</html>