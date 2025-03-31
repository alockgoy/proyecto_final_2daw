<?php 
require_once __DIR__ . '/../models/Serie.php';
require_once __DIR__ . '/../config.php';

class SerieController{
    private $serieModel;

    // Constructor
    public function __construct() {
        global $pdo;
        $this->serieModel = new Serie($pdo);
    }

    // Mostrar todas las series
    public function index() {
        $series = $this->serieModel->getAllSeries();
        require_once __DIR__ . '/../vistas/series.php';
    }

    // Añadir una serie
    public function addMovie() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->serieModel->createSerie($_POST["name"], $_POST["poster"], $_POST["gender"], $_POST["languages"], $_POST["seasons"], $_POST["complete"], $_POST["year"], $_POST["quality"], $_POST["backup"], $_POST["size"], $_POST["server"], $_POST["rating"]
            );
            header("Location: series.php");
        }
        include __DIR__ . '/../views/add_serie.php';
    }

    // Editar una serie
    public function editSerie($id) {
        $serie = $this->serieModel->getSerieById($id);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->serieModel->updateSerie($id, $_POST["name"], $_POST["poster"], $_POST["gender"], $_POST["languages"], $_POST["seasons"], $_POST["complete"], $_POST["year"], $_POST["quality"], $_POST["backup"], $_POST["size"], $_POST["server"], $_POST["rating"]);
            header("Location: series.php");
        }
        include __DIR__ . '/../views/edit_serie.php';
    }

    // Eliminar una serie
    public function deleteSerie($id) {
        $this->serieModel->deleteSerie($id);
        header("Location: series.php");
    }
}

?>