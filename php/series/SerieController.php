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
    public function editSerie($id)
    {
        $serie = $this->serieModel->getSerieById($id);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->serieModel->updateSerie($id, $_POST["name"], $_POST["poster"], $_POST["gender"], $_POST["languages"], $_POST["seasons"], $_POST["complete"], $_POST["year"], $_POST["quality"], $_POST["backup"], $_POST["size"], $_POST["server"], $_POST["rating"]);
            header("Location: series.php");
        }
        include __DIR__ . '/../views/edit_serie.php';
    }

    // Eliminar una serie
    public function deleteSerie($id)
    {
        $this->serieModel->deleteSerie($id);
        header("Location: series.php");
    }
}

?>