<?php
require_once __DIR__ . '/Serie.php';
require_once __DIR__ . '/../config.php';

class SerieController
{
    private $serieModel;

    // Constructor
    public function __construct()
    {
        global $pdo;
        $this->serieModel = new Serie($pdo);
    }

    // Mostrar todas las series
    public function index()
    {
        $series = $this->serieModel->getAllSeries();
        return $series;
    }

    // Añadir una serie
    public function addSerie() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $posterPath = '';
            
            // Procesar la imagen si ha sido subida
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
                $targetDir = __DIR__ . "/../../img/portadas_series/";
                $fileName = basename($_FILES["poster"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                
                // Verificar que el tamaño del archivo no exceda 5 MB
                if ($_FILES["poster"]["size"] > 5 * 1024 * 1024) {
                    throw new Exception("El archivo de imagen no puede pesar más de 5 MB.");
                }
                
                // Verificar que sea una imagen válida
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
                
                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Subir el archivo
                    if (move_uploaded_file($_FILES["poster"]["tmp_name"], $targetFilePath)) {
                        $posterPath = "img/portadas_series/" . $fileName;
                    } else {
                        throw new Exception("Error al subir el archivo de imagen.");
                    }
                } else {
                    throw new Exception("Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.");
                }
            } else {
                throw new Exception("Debes seleccionar una imagen para el póster.");
            }
            
            // Crear la serie con la ruta de la imagen - ajustado a la estructura real de la tabla
            $this->serieModel->createSerie(
                $_POST["name"],
                $posterPath,
                $_POST["gender"],
                $_POST["languages"],
                $_POST["seasons"],
                $_POST["complete"] ?? "no",
                $_POST["year"],
                $_POST["quality"],
                $_POST["backup"] ?? null,
                $_POST["size"],
                $_POST["server"],
                $_POST["rating"] ?? null
            );
            
            return true;
        }
        return false;
    }

    // Obtener una serie por ID
    public function getSerie($id) {
        return $this->serieModel->getSerieById($id);
    }

    // Editar una serie
    public function updateSerie($id) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $posterPath = null;
            
            // Obtener la serie actual para saber la ruta de la imagen actual
            $currentSerie = $this->serieModel->getSerieById($id);
            
            if (!$currentSerie) {
                throw new Exception("La serie no existe");
            }
            
            // Usar la ruta de la imagen actual si no se está subiendo una nueva
            $posterPath = $currentSerie['poster'];
            
            // Procesar la imagen si ha sido subida
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
                $targetDir = __DIR__ . "/../../img/portadas_series/";
                $fileName = basename($_FILES["poster"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                
                // Verificar que el tamaño del archivo no exceda 5 MB
                if ($_FILES["poster"]["size"] > 5 * 1024 * 1024) {
                    throw new Exception("El archivo de imagen no puede pesar más de 5 MB.");
                }
                
                // Verificar que sea una imagen válida
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
                
                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Subir el archivo
                    if (move_uploaded_file($_FILES["poster"]["tmp_name"], $targetFilePath)) {
                        // Borrar el archivo anterior si existe y es diferente
                        if ($currentSerie['poster'] && $currentSerie['poster'] != "img/portadas_series/" . $fileName) {
                            $oldPosterPath = __DIR__ . "/../../" . $currentSerie['poster'];
                            if (file_exists($oldPosterPath)) {
                                unlink($oldPosterPath);
                            }
                        }
                        
                        $posterPath = "img/portadas_series/" . $fileName;
                    } else {
                        throw new Exception("Error al subir el archivo de imagen.");
                    }
                } else {
                    throw new Exception("Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.");
                }
            }
            
            // Actualizar la serie en la base de datos
            $result = $this->serieModel->updateSerie(
                $id,
                $_POST["name"],
                $posterPath,
                $_POST["gender"],
                $_POST["languages"],
                $_POST["seasons"],
                $_POST["complete"],
                $_POST["year"],
                $_POST["quality"],
                $_POST["backup"] ?? null,
                $_POST["size"],
                $_POST["server"],
                $_POST["rating"] ?? null
            );
            
            if (!$result) {
                throw new Exception("No se pudo actualizar la serie");
            }
            
            return true;
        }
        return false;
    }

    // Eliminar una serie
    public function deleteSerie($id)
    {
        $this->serieModel->deleteSerie($id);
        header("Location: series.php");
    }

    // Obtener las series vinculadas al usuario
    public function getSeriesByUserId($userId) {
        return $this->serieModel->getSeriesByUserId($userId);
    }

    // Comprobar que una serie está vinculada con un usuario
    public function checkSerieBelongsToUser($serieId, $userId) {
        return $this->serieModel->checkSerieBelongsToUser($serieId, $userId);
    }

    // Obtener el ID de la última serie añadida
    public function getLastInsertedId() {
        return $this->serieModel->getLastInsertedId();
    }

    // Asociar series con usuarios
    public function associateSerieWithUser($serieId, $userId) {
        return $this->serieModel->associateSerieWithUser($serieId, $userId);
    }

    // Borrar series que no tienen usuario asociado
    public function deleteSeriesWithoutUsers(){
        // Obtener series sin usuario
        $series = $this->serieModel->getSeriesWithoutUser();

        // Eliminar pósters antes de eliminar los registros
        foreach ($series as $serie) {
            if (!empty($serie['poster'])) {
                $posterPath = __DIR__ . '/../../' . $serie['poster'];

                if (file_exists($posterPath)) {
                    if (unlink($posterPath)) {
                        error_log("Póster eliminado exitosamente");
                    } else {
                        error_log("Error al eliminar póster");
                    }
                } else {
                    error_log("El archivo del póster no existe");
                }
            }
        }

        // Después de eliminar todos los pósters, eliminar los registros de la base de datos
        $result = $this->serieModel->deleteSeriesWithoutUsers();

        error_log("Resultado de eliminar series: " . ($result ? "Éxito" : "Fallo"));
        return $result;
    }
}

?>