<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/series/SerieController.php';
require_once '../../php/series/Serie.php';
require_once '../../php/series/EpisodioController.php';
require_once '../../php/series/Episodio.php';
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';
require_once '../../php/calidades/QualityController.php';
require_once '../../php/calidades/Quality.php';
require_once '../../php/seguridad.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

$controller = new SerieController();
$episodioController = new EpisodioController();
$userController = new UserController();
$qualityController = new QualityController();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha especificado una serie.');
}

$id = $_GET['id'];
$serie = $controller->getSerie($id);

$userId = $userController->getUserIdByUsername($_SESSION['username']);
$isOwner = $controller->checkSerieBelongsToUser($id, $userId);

if (!$isOwner) {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

if (!$serie) {
    die('Error: La serie solicitada no existe.');
}

// Obtener episodios agrupados por temporada
$episodios = $episodioController->getEpisodiosBySerieId($id);
$episodiosPorTemporada = [];
foreach ($episodios as $ep) {
    $episodiosPorTemporada[$ep['temporada']][] = $ep;
}

// Tamaño total calculado desde episodios
$totalSize = $episodioController->getTotalSize($id);

// Obtener todas las calidades para los formularios
$qualities = $qualityController->index();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/series/show_serie.css" type="text/css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/pelicula.png" type="image/x-icon">
    <title><?php echo htmlspecialchars($serie['name']); ?></title>
</head>

<body>

<div class="container mt-4 mb-5">
    <div class="row">

        <!-- Poster -->
        <aside class="col-md-4">
            <img src="../../<?php echo htmlspecialchars($serie['poster']); ?>"
                alt="<?php echo htmlspecialchars($serie['name']); ?>" class="img-fluid rounded shadow">
        </aside>

        <!-- Detalles generales -->
        <main class="col-md-8">
            <h2><?php echo htmlspecialchars($serie['name']); ?></h2>

            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Género(s):</strong> <?php echo htmlspecialchars($serie['gender']); ?></li>
                <li class="list-group-item"><strong>Año:</strong> <?php echo htmlspecialchars($serie['year']); ?></li>
                <li class="list-group-item"><strong>Idioma(s):</strong> <?php echo htmlspecialchars($serie['languages']); ?></li>
                <li class="list-group-item"><strong>Temporadas:</strong> <?php echo htmlspecialchars($serie['seasons']); ?></li>
                <li class="list-group-item"><strong>Completa:</strong> <?php echo $serie['complete'] == 'si' ? 'Sí' : 'No'; ?></li>
                <?php if ($totalSize > 0): ?>
                <li class="list-group-item"><strong>Tamaño total:</strong> <?php echo round($totalSize, 2); ?> GB</li>
                <?php endif; ?>
                <?php if (!empty($serie['rating'])): ?>
                <li class="list-group-item"><strong>Valoración:</strong> <?php echo htmlspecialchars($serie['rating']); ?>/10</li>
                <?php endif; ?>
            </ul>

            <?php if (isset($serie['synopsis']) && !empty($serie['synopsis'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title h5 mb-0">Sinopsis</h3>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo htmlspecialchars($serie['synopsis']); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                <a href="edit_serie.php?id=<?php echo $serie['id_serie']; ?>" class="btn btn-warning me-md-2">
                    <i class="bi bi-pencil-square"></i> Editar serie
                </a>
            </div>
        </main>
    </div>

    <!-- ── Sección de episodios ── -->
    <hr class="my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-collection-play me-2"></i>Episodios</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importarTemporadaModal">
            <i class="bi bi-cloud-download me-1"></i> Importar temporada
        </button>
    </div>

    <?php if (empty($episodiosPorTemporada)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Esta serie todavía no tiene episodios registrados. Usa el botón "Importar temporada" para añadirlos.
        </div>
    <?php else: ?>
        <?php foreach ($episodiosPorTemporada as $temporada => $eps): ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Temporada <?php echo $temporada; ?></strong>
                <button class="btn btn-sm btn-outline-danger"
                    onclick="confirmarBorrarTemporada(<?php echo $serie['id_serie']; ?>, <?php echo $temporada; ?>)">
                    <i class="bi bi-trash"></i> Borrar temporada
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Ep.</th>
                            <th>Calidad</th>
                            <th>Tamaño (GB)</th>
                            <th>Backup</th>
                            <th>Servidor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eps as $ep): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ep['episodio']); ?></td>
                            <td><?php echo htmlspecialchars($ep['quality_name']); ?></td>
                            <td><?php echo htmlspecialchars($ep['size']); ?></td>
                            <td><?php echo !empty($ep['backup']) ? htmlspecialchars($ep['backup']) : '—'; ?></td>
                            <td><?php echo $ep['server'] == 'si' ? 'Sí' : 'No'; ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning"
                                    onclick="abrirEditarEpisodio(<?php echo htmlspecialchars(json_encode($ep)); ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmarBorrarEpisodio(<?php echo $ep['id_episodio']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Footer fijo -->
<footer class="bg-dark text-white text-center py-3 fixed-bottom">
    <div class="container footer-container d-flex justify-content-center align-items-center">
        <form method="POST" action="delete_serie.php" style="display:inline;" id="deleteSerieForm">
            <?php echo campoTokenCSRF(); ?>
            <input type="hidden" name="id" value="<?php echo $serie['id_serie']; ?>">
            <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteSerieModal">
                Borrar Serie
            </button>
        </form>
        <a href="series.php" class="btn btn-secondary">Volver Atrás</a>
    </div>
</footer>

<!-- Modal: Importar temporada -->
<div class="modal fade" id="importarTemporadaModal" tabindex="-1" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cloud-download me-2"></i>Importar temporada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Temporada</label>
                    <input type="number" class="form-control" id="import_temporada" min="1" value="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Calidad <span class="text-danger">*</span></label>
                    <select class="form-select" id="import_quality">
                        <option value="" disabled selected>Selecciona la calidad</option>
                        <?php foreach ($qualities as $q): ?>
                        <option value="<?php echo $q['id_quality']; ?>"><?php echo htmlspecialchars($q['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tamaño por episodio (GB) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="import_size" step="0.5" min="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">Backup</label>
                    <input type="text" class="form-control" id="import_backup" placeholder="Ej: NAS">
                </div>
                <div class="mb-3">
                    <label class="form-label">¿En servidor? <span class="text-danger">*</span></label>
                    <select class="form-select" id="import_server">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div id="import_error" class="alert alert-danger d-none"></div>
                <div id="import_loading" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Importando episodios desde TMDB...</p>
                </div>
                <div id="import_success" class="alert alert-success d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="importarTemporada()">
                    <i class="bi bi-cloud-download me-1"></i>Importar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar episodio -->
<div class="modal fade" id="editarEpisodioModal" tabindex="-1" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar episodio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id_episodio">
                <p id="edit_episodio_label" class="fw-bold"></p>
                <div class="mb-3">
                    <label class="form-label">Calidad</label>
                    <select class="form-select" id="edit_quality">
                        <?php foreach ($qualities as $q): ?>
                        <option value="<?php echo $q['id_quality']; ?>"><?php echo htmlspecialchars($q['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tamaño (GB)</label>
                    <input type="number" class="form-control" id="edit_size" step="0.01" min="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">Backup</label>
                    <input type="text" class="form-control" id="edit_backup">
                </div>
                <div class="mb-3">
                    <label class="form-label">¿En servidor?</label>
                    <select class="form-select" id="edit_server">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div id="edit_error" class="alert alert-danger d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarEpisodio()">
                    <i class="bi bi-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Borrar serie -->
<div class="modal fade" id="deleteSerieModal" tabindex="-1" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Confirmación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-light">
                ¿Estás seguro de que deseas eliminar la serie "<?php echo htmlspecialchars($serie['name']); ?>"?
                Se eliminarán también todos sus episodios.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger"
                    onclick="document.getElementById('deleteSerieForm').submit();">Borrar Serie</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>const TMDB_API_KEY = '<?= defined("TMDB_API_KEY") ? TMDB_API_KEY : "" ?>';</script>
<script>const SERIE_ID = <?php echo $serie['id_serie']; ?>;</script>
<script>const SERIE_TMDB_ID = <?php echo !empty($serie['tmdb_id']) ? $serie['tmdb_id'] : 'null'; ?>;</script>
<script src="../../js/series/tmdb_episodes.js"></script>
<script src="../../js/confirm_modal.js"></script>

</body>
</html>
