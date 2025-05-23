<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

// Traer los archivos necesarios
require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Crear instancia del controlador
$controller = new SerieController();
$userController = new UserController();

// Variable del mensaje de error si algo salió mal
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Añadir la serie con la validación incorporada
        if ($controller->addSerie()) {
            // Obtener el ID de la última serie insertada
            $serieId = $controller->getLastInsertedId();

            // Obtener el ID del usuario actual
            $userId = $userController->getUserIdByUsername($_SESSION['username']);

            // Asociar la serie con el usuario
            $result = $controller->associateSerieWithUser($serieId, $userId);

            if ($result) {
                $success = "Serie añadida correctamente, redirigiendo...";
                //header("Location: series.php");
                //exit();
            } else {
                $error = ("Error al asociar la serie con el usuario.");
            }
        } else {
            // Obtener el error de validación del controlador
            $error = $controller->lastError ?: "Error al añadir la película.";
        }
    } catch (Exception $e) {
        $error = ("Error al añadir la serie: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Serie</title>
    <link href="../../css/add_serie.css" type="text/css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../img/iconos_navegador/serie.png" type="image/x-icon" />
</head>

<body class="bg-light">
    <div class="container form-container py-4">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0"><i class="fas fa-tv me-2"></i>Añadir Nueva Serie</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert" data-redirect="./series.php">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Nombre de la serie -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tv"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nombre"
                                        required />
                                    <label for="name">Nombre de la serie</label>
                                </div>
                            </div>
                        </div>

                        <!-- Año -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="year" name="year" placeholder="Año"
                                        required />
                                    <label for="year">Año</label>
                                </div>
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-theater-masks"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="" selected disabled>Selecciona un género</option>
                                        <option value="acción/aventura">Acción/Aventura</option>
                                        <option value="animación">Animación</option>
                                        <option value="anime">Anime</option>
                                        <option value="ciencia ficción">Ciencia Ficción</option>
                                        <option value="cortometraje">Cortometraje</option>
                                        <option value="comedia">Comedia</option>
                                        <option value="deportes">Deportes</option>
                                        <option value="documental">Documental</option>
                                        <option value="drama">Drama</option>
                                        <option value="familiar">Familiar</option>
                                        <option value="fantasía">Fantasía</option>
                                        <option value="guerra">Guerra</option>
                                        <option value="terror">Terror</option>
                                        <option value="musical">Musical</option>
                                        <option value="suspense">Suspense</option>
                                        <option value="romance">Romance</option>
                                        <option value="vaqueros">Vaqueros</option>
                                        <option value="misterio">Misterio</option>
                                    </select>
                                    <label for="gender">Género</label>
                                </div>
                            </div>
                        </div>

                        <!-- Temporadas -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="seasons" name="seasons" min="1"
                                        placeholder="Temporadas" required />
                                    <label for="seasons">Temporadas</label>
                                </div>
                            </div>
                        </div>

                        <!-- Completa -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="complete" name="complete" required>
                                        <option value="" selected disabled>Selecciona</option>
                                        <option value="si">Sí</option>
                                        <option value="no">No</option>
                                    </select>
                                    <label for="complete">¿Completa?</label>
                                </div>
                            </div>
                        </div>

                        <!-- Idiomas -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-language"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="languages" name="languages"
                                        placeholder="Idiomas" required />
                                    <label for="languages">Idiomas (ej: Español, Inglés)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Calidad -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-photo-video"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="quality" name="quality" required>
                                        <option value="" selected disabled>Selecciona la calidad</option>
                                        <option value="4K">4K</option>
                                        <option value="1440p">1440p</option>
                                        <option value="1080p">1080p</option>
                                        <option value="720p">720p</option>
                                        <option value="420p">420p</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                    <label for="quality">Calidad</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tamaño -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hdd"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="size" name="size" placeholder="Tamaño"
                                        step="0.1" required />
                                    <label for="size">Tamaño (GB)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Calificación -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="rating" name="rating"
                                        placeholder="Calificación" min="1" max="10" />
                                    <label for="rating">Calificación (1-10)</label>
                                </div>
                            </div>
                        </div>

                        <!-- En servidor -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-server"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="server" name="server" required>
                                        <option value="" selected disabled>¿En servidor?</option>
                                        <option value="si">Sí</option>
                                        <option value="no">No</option>
                                    </select>
                                    <label for="server">¿En servidor?</label>
                                </div>
                            </div>
                        </div>

                        <!-- Backup URL -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="backup" name="backup"
                                        placeholder="¿Dónde está la copia de seguridad?" />
                                    <label for="backup">Backup (URL)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Poster -->
                        <div class="col-12 form-group">
                            <label for="poster" class="form-label"><i class="fas fa-image me-2"></i>Poster</label>
                            <input type="file" class="form-control" id="poster" name="poster" accept="image/*" required />
                            <div class="invalid-feedback">
                                Por favor, selecciona una imagen para el poster.
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <a href="series.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Serie
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enlace al JS de bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al Javascript de añadir series -->
    <script src="../../js/add_serie.js"></script>

</body>
</html>