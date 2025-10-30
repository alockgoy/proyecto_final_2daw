<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../peliculas/MovieController.php';
require_once __DIR__ . '/../series/SerieController.php';
require_once __DIR__ . '/../usuarios/UserController.php';

class BackupController
{
    private $movieController;
    private $serieController;
    private $userController;
    public $lastError = "";

    public function __construct()
    {
        $this->movieController = new MovieController();
        $this->serieController = new SerieController();
        $this->userController = new UserController();
    }

    /**
     * Exportar backup completo del usuario
     */
    public function exportBackup($userId)
    {
        try {
            // Obtener todas las películas y series del usuario
            $movies = $this->movieController->getMoviesByUserId($userId);
            $series = $this->serieController->getSeriesByUserId($userId);

            // Crear estructura de datos
            $backupData = [
                'version' => '1.0',
                'export_date' => date('Y-m-d H:i:s'),
                'user_id' => $userId,
                'movies' => [],
                'series' => []
            ];

            // Procesar películas
            foreach ($movies as $movie) {
                $movieData = $movie;
                
                // Leer el archivo de imagen y convertirlo a base64
                if (!empty($movie['poster'])) {
                    $posterPath = __DIR__ . '/../../' . $movie['poster'];
                    if (file_exists($posterPath)) {
                        $imageData = file_get_contents($posterPath);
                        $movieData['poster_data'] = base64_encode($imageData);
                        $movieData['poster_extension'] = pathinfo($movie['poster'], PATHINFO_EXTENSION);
                    }
                }
                
                $backupData['movies'][] = $movieData;
            }

            // Procesar series
            foreach ($series as $serie) {
                $serieData = $serie;
                
                // Leer el archivo de imagen y convertirlo a base64
                if (!empty($serie['poster'])) {
                    $posterPath = __DIR__ . '/../../' . $serie['poster'];
                    if (file_exists($posterPath)) {
                        $imageData = file_get_contents($posterPath);
                        $serieData['poster_data'] = base64_encode($imageData);
                        $serieData['poster_extension'] = pathinfo($serie['poster'], PATHINFO_EXTENSION);
                    }
                }
                
                $backupData['series'][] = $serieData;
            }

            // Convertir a JSON
            $jsonData = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if ($jsonData === false) {
                throw new Exception("Error al generar el archivo de backup");
            }

            // Crear nombre de archivo
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.json';

            // Enviar headers para descarga
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($jsonData));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            echo $jsonData;
            exit();

        } catch (Exception $e) {
            $this->lastError = "Error al exportar backup: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    /**
     * Importar backup completo
     */
    public function importBackup($userId, $file)
    {
        try {
            // Validar archivo
            if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir el archivo de backup");
            }

            // Verificar que sea un archivo JSON
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'json') {
                throw new Exception("El archivo debe ser un JSON");
            }

            // Leer contenido del archivo
            $jsonContent = file_get_contents($file['tmp_name']);
            $backupData = json_decode($jsonContent, true);

            if ($backupData === null) {
                throw new Exception("El archivo JSON no es válido");
            }

            // Validar estructura del backup
            if (!isset($backupData['version']) || !isset($backupData['movies']) || !isset($backupData['series'])) {
                throw new Exception("El archivo de backup no tiene la estructura correcta");
            }

            $importedMovies = 0;
            $importedSeries = 0;
            $errors = [];

            // Importar películas
            foreach ($backupData['movies'] as $movieData) {
                try {
                    // Restaurar imagen
                    $posterPath = null;
                    if (isset($movieData['poster_data']) && isset($movieData['poster_extension'])) {
                        $imageData = base64_decode($movieData['poster_data']);
                        
                        $targetDir = __DIR__ . "/../../img/portadas_peliculas/";
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }

                        $nombreUnicoArchivo = uniqid("poster_") . "_imported." . $movieData['poster_extension'];
                        $rutaPoster = $targetDir . $nombreUnicoArchivo;

                        if (file_put_contents($rutaPoster, $imageData) !== false) {
                            $posterPath = "img/portadas_peliculas/" . $nombreUnicoArchivo;
                        }
                    }

                    // Crear película (sin el ID original)
                    $result = $this->movieController->getMovie(0); // Acceder al modelo
                    
                    // Usar el modelo directamente para crear la película
                    global $pdo;
                    $movieModel = new Movie($pdo);
                    
                    $rating = isset($movieData['rating']) && $movieData['rating'] !== '' ? (float)$movieData['rating'] : null;
                    
                    $created = $movieModel->createMovie(
                        $movieData['name'],
                        $movieData['synopsis'] ?? '',
                        $posterPath ?? '',
                        $movieData['director'],
                        $movieData['gender'],
                        $movieData['languages'],
                        $movieData['size'],
                        $movieData['year'],
                        $movieData['id_quality'],
                        $movieData['backup'] ?? null,
                        $movieData['server'],
                        $rating
                    );

                    if ($created) {
                        // Asociar con el usuario
                        $lastMovieId = $movieModel->getLastInsertedId();
                        $movieModel->associateMovieWithUser($lastMovieId, $userId);
                        $importedMovies++;
                    }

                } catch (Exception $e) {
                    $errors[] = "Error importando película '" . ($movieData['name'] ?? 'desconocida') . "': " . $e->getMessage();
                    error_log($e->getMessage());
                }
            }

            // Importar series
            foreach ($backupData['series'] as $serieData) {
                try {
                    // Restaurar imagen
                    $posterPath = null;
                    if (isset($serieData['poster_data']) && isset($serieData['poster_extension'])) {
                        $imageData = base64_decode($serieData['poster_data']);
                        
                        $targetDir = __DIR__ . "/../../img/portadas_series/";
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }

                        $nombreUnicoArchivo = uniqid("poster_") . "_imported." . $serieData['poster_extension'];
                        $rutaPoster = $targetDir . $nombreUnicoArchivo;

                        if (file_put_contents($rutaPoster, $imageData) !== false) {
                            $posterPath = "img/portadas_series/" . $nombreUnicoArchivo;
                        }
                    }

                    // Crear serie
                    global $pdo;
                    $serieModel = new Serie($pdo);
                    
                    $rating = isset($serieData['rating']) && $serieData['rating'] !== '' ? (float)$serieData['rating'] : null;
                    
                    $created = $serieModel->createSerie(
                        $serieData['name'],
                        $posterPath ?? '',
                        $serieData['gender'],
                        $serieData['languages'],
                        $serieData['seasons'],
                        $serieData['complete'] ?? 'no',
                        $serieData['year'],
                        $serieData['id_quality'],
                        $serieData['backup'] ?? null,
                        $rating,
                        $serieData['server'],
                        $serieData['size']
                    );

                    if ($created) {
                        // Asociar con el usuario
                        $lastSerieId = $serieModel->getLastInsertedId();
                        $serieModel->associateSerieWithUser($lastSerieId, $userId);
                        $importedSeries++;
                    }

                } catch (Exception $e) {
                    $errors[] = "Error importando serie '" . ($serieData['name'] ?? 'desconocida') . "': " . $e->getMessage();
                    error_log($e->getMessage());
                }
            }

            return [
                'success' => true,
                'imported_movies' => $importedMovies,
                'imported_series' => $importedSeries,
                'errors' => $errors
            ];

        } catch (Exception $e) {
            $this->lastError = "Error al importar backup: " . $e->getMessage();
            error_log($this->lastError);
            return [
                'success' => false,
                'message' => $this->lastError
            ];
        }
    }
}
?>