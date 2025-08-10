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
require_once '../../php/peliculas/MovieController.php';
require_once '../../php/peliculas/Movie.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';
require_once '../../php/calidades/QualityController.php';
require_once '../../php/calidades/Quality.php';
require_once '../../php/movimientos/MovementController.php';
require_once '../../php/movimientos/Movement.php';
require_once '../../php/api/ApiController.php';
require_once '../../php/api/api_config.php';

// Crear instancia del controlador
$controller = new MovieController();
$userController = new UserController();
$qualityController = new QualityController();
$movementController = new MovementController();
$apiController = new ApiController();

// Variable del mensaje de error si algo salió mal
$error = "";

// Variable para almacenar resultados de búsqueda
$searchResults = [];

if (isset($_POST['search_movie'])) {
    $searchQuery = trim($_POST['search_query']);
    if (!empty($searchQuery)) {
        $searchResults = $apiController->searchMovie($searchQuery);

        // Verificar si hay error en la API
        if (isset($searchResults['error'])) {
            $apiError = $searchResults['error'];
            $searchResults = [];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['search_movie'])) {
    try {
        // Añadir la película con la validación incorporada
        if ($controller->addMovie()) {
            // Obtener el ID de la última película insertada
            $movieId = $controller->getLastInsertedId();

            // Obtener el ID del usuario actual
            $userId = $userController->getUserIdByUsername($_SESSION['username']);

            // Asociar la película con el usuario
            $result = $controller->associateMovieWithUser($movieId, $userId);

            if ($result) {
                $movieData = $controller->getMovie($movieId);
                $movieName = $movieData ? $movieData['name'] : 'serie desconocida';
                $movementController->addMovement($_SESSION['username'], "ha añadido la película $movieName", date('Y-m-d H:i:s'), "correcto");
                $success = "Película añadida correctamente, redirigiendo...";
                //header("Location: movies.php");
                //exit();
            } else {
                $error = "Error al asociar la película con el usuario.";
            }
        } else {
            // Obtener el error de validación del controlador
            $error = $controller->lastError ?: "Error al añadir la película.";
        }
    } catch (Exception $e) {
        $error = "Error al añadir la película: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Añadir Película</title>
    <link href="../../css/peliculas/add_movie.css" type="text/css" rel="stylesheet" />
    <!-- Enlace al CSS de bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon" />
</head>

<body class="bg-light">
    <div class="container form-container py-4">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0"><i class="fas fa-film me-2"></i>Añadir Película</h2>

                <div class="card-body border-bottom">
                    <h5><i class="fas fa-search me-2"></i>Buscar en TheMovieDB</h5>
                    <form method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search_query" placeholder="Buscar película..."
                                value="<?php echo isset($_POST['search_query']) ? htmlspecialchars($_POST['search_query']) : ''; ?>">
                            <button type="submit" name="search_movie" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>

                    <?php if (!empty($searchResults['results'])): ?>
                        <div class="row">
                            <?php foreach (array_slice($searchResults['results'], 0, 5) as $movie): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="card h-100">
                                        <div class="row g-0">
                                            <div class="col-4">
                                                <?php if ($movie['poster_path']): ?>
                                                    <img src="<?php echo TMDBConfig::IMAGE_BASE_URL . $movie['poster_path']; ?>"
                                                        class="img-fluid rounded-start h-100" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                                        <i class="fas fa-film fa-2x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-8">
                                                <div class="card-body p-2">
                                                    <h6 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h6>
                                                    <p class="card-text small">
                                                        <?php echo htmlspecialchars(substr($movie['overview'], 0, 100)); ?>...
                                                    </p>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        onclick="fillMovieData(<?php echo $movie['id']; ?>)">
                                                        <i class="fas fa-plus"></i> Usar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert" data-redirect="./movies.php">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Nombre de la película -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-film"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nombre"
                                        required />
                                    <label for="name">Nombre de la película *</label>
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
                                    <label for="year">Año *</label>
                                </div>
                            </div>
                        </div>

                        <!-- Director -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="director" name="director"
                                        placeholder="Director" required />
                                    <label for="director">Director *</label>
                                </div>
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-theater-masks"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="gender" name="gender"
                                        placeholder="Género" required />
                                    <label for="gender">Género</label>
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
                                    <label for="languages">Idiomas * (ej: Español, Inglés)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Calidad -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-photo-video"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <select class="form-select" id="id_quality" name="id_quality" required>
                                        <option value="" selected disabled>Selecciona la calidad</option>
                                        <?php
                                        // Obtener todas las calidades disponibles desde el controlador
                                        $qualities = $qualityController->index();
                                        foreach ($qualities as $quality) {
                                            echo '<option value="' . htmlspecialchars($quality['id_quality']) . '">' . htmlspecialchars($quality['name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label for="quality">Calidad *</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tamaño -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hdd"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="size" name="size" step="0.1"
                                        placeholder="Tamaño" required />
                                    <label for="size">Tamaño * (GB)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Calificación -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="number" class="form-control" id="rating" name="rating"
                                        placeholder="Calificación" step="0.1" min="1" max="10" />
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
                                    <label for="server">¿En servidor? *</label>
                                </div>
                            </div>
                        </div>

                        <!-- Backup -->
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" class="form-control" id="backup" name="backup"
                                        placeholder="¿Dónde está la copia de seguridad?" />
                                    <label for="backup">Backup</label>
                                </div>
                            </div>
                        </div>

                        <!-- Poster -->
                        <div class="col-12 form-group">
                            <label for="poster" class="form-label"><i class="fas fa-image me-2"></i>Poster *</label>
                            <div class="d-flex align-items-center">
                                <input type="file" class="form-control" id="poster" name="poster" accept="image/*"
                                    required />
                                <div class="invalid-feedback">
                                    Por favor, selecciona una imagen para el poster. *
                                </div>
                                <a href="#" id="clear" class="btn btn-outline-danger ms-2"
                                    title="Eliminar foto de perfil">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>

                        </div>

                        <!-- Sinopsis -->
                        <div class="col-12 form-group">
                            <label for="synopsis" class="form-label"><i
                                    class="fas fa-align-left me-2"></i>Sinopsis</label>
                            <textarea class="form-control" id="synopsis" name="synopsis" rows="4"
                                placeholder="Escribe una breve sinopsis de la película..."></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <a href="movies.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enlace al Javascript de bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al Javascript de añadir películas -->
    <script src="../../js/peliculas/add_movie.js"></script>

    <!-- Enlace al archivo JS que permite limpiar el archivo del formulario -->
    <script src="../../js/peliculas/delete_input_file.js"></script>

    <script>
        function fillMovieData(movieId) {
            console.log('fillMovieData llamada con ID:', movieId);
            fetch(`../../php/api/ApiComponent.php?id=${movieId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Datos recibidos:', data);

                    // Rellenar campos del formulario
                    if (data.name) document.getElementById('name').value = data.name;
                    if (data.year) document.getElementById('year').value = data.year;
                    if (data.synopsis) document.getElementById('synopsis').value = data.synopsis;
                    if (data.genres) document.getElementById('gender').value = data.genres;
                    if (data.rating) document.getElementById('rating').value = data.rating;

                    // Scroll al formulario
                    document.getElementById('name').scrollIntoView({ behavior: 'smooth' });

                    // Mostrar notificación de éxito
                    showNotification('Datos cargados desde TheMovieDB', 'success');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al cargar los datos de la película', 'danger');
                });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(notification, cardBody.firstChild);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        function downloadAndSetPoster(posterUrl, movieName) {
            // Mostrar que se está descargando
            const posterInput = document.getElementById('poster');
            const posterLabel = document.querySelector('label[for="poster"]');

            posterLabel.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Descargando poster...';

            // Crear un enlace para descargar la imagen
            fetch(`../../php/api/DownloadPoster.php?url=${encodeURIComponent(posterUrl)}&name=${encodeURIComponent(movieName)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Simular la selección del archivo
                        posterLabel.innerHTML = '<i class="fas fa-check me-2"></i>Poster cargado: ' + data.filename;
                        posterInput.setAttribute('data-auto-poster', data.filename);
                        posterInput.removeAttribute('required');

                        showNotification('Poster descargado automáticamente', 'success');
                    } else {
                        posterLabel.innerHTML = '<i class="fas fa-image me-2"></i>Poster *';
                        showNotification('No se pudo descargar el poster automáticamente', 'warning');
                    }
                })
                .catch(error => {
                    console.error('Error descargando poster:', error);
                    posterLabel.innerHTML = '<i class="fas fa-image me-2"></i>Poster *';
                    showNotification('Error al descargar el poster', 'warning');
                });
            }
            console.log('JavaScript inline cargado');
    </script>
</body>

</html>