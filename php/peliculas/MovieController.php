<?php
require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../config.php';

class MovieController {
    private $movieModel;

    // Constructor
    public function __construct() {
        global $pdo;
        $this->movieModel = new Movie($pdo);
    }

    // Mostrar todas las películas
    public function index() {
        $movies = $this->movieModel->getAllMovies();
        include __DIR__ . '/../views/movies.php';
    }

    // Añadir una película
    public function addMovie() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->movieModel->createMovie($_POST["name"], $_POST["synopsis"], $_POST["poster"], $_POST["director"], $_POST["gender"], $_POST["languages"], $_POST["size"], $_POST["year"], $_POST["quality"], $_POST["backup"], $_POST["server"], $_POST["rating"]);
            header("Location: movies.php");
        }
        include __DIR__ . '/../views/add_movie.php';
    }

    // Editar una película
    public function editMovie($id) {
        $movie = $this->movieModel->getMovieById($id);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->movieModel->updateMovie($id, $_POST["name"], $_POST["synopsis"], $_POST["poster"], $_POST["director"], $_POST["gender"], $_POST["languages"], $_POST["size"], $_POST["year"], $_POST["quality"], $_POST["backup"], $_POST["server"], $_POST["rating"]);
            header("Location: movies.php");
        }
        include __DIR__ . '/../views/edit_movie.php';
    }

    // Eliminar una película
    public function deleteMovie($id) {
        $this->movieModel->deleteMovie($id);
        header("Location: movies.php");
    }
}
?>
