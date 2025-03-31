<?php
require_once __DIR__ . '/Movie.php';
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
        return $movies;
        //include __DIR__ . '/../views/movies.php';
    }

    // Añadir una película
    public function addMovie() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $posterPath = '';
            
            // Procesar la imagen si ha sido subida
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
                $targetDir = __DIR__ . "/../../img/portadas_peliculas/";
                $fileName = basename($_FILES["poster"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                
                // Verificar que sea una imagen válida
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
                
                // Verificar que el tamaño del archivo no exceda 5 MB
                if ($_FILES["poster"]["size"] > 5 * 1024 * 1024) {
                    echo("El archivo de imagen no puede pesar más de 5 MB.");
                } else {
                    if (in_array(strtolower($fileType), $allowTypes)) {
                        // Subir el archivo
                        if (move_uploaded_file($_FILES["poster"]["tmp_name"], $targetFilePath)) {
                            $posterPath = "img/portadas_peliculas/" . $fileName;
                        } else {
                            throw new Exception("Error al subir el archivo de imagen.");
                        }
                    } else {
                        throw new Exception("Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.");
                    }
                }
                
            } else {
                throw new Exception("Debes seleccionar una imagen para el póster.");
            }
            
            // Crear la película con la ruta de la imagen
            $this->movieModel->createMovie(
                $_POST["name"],
                $_POST["synopsis"],
                $posterPath, // Ruta guardada de la imagen
                $_POST["director"],
                $_POST["gender"],
                $_POST["languages"],
                $_POST["size"],
                $_POST["year"],
                $_POST["quality"],
                $_POST["backup"] ?? null,
                $_POST["server"],
                $_POST["rating"] ?? null
            );
            
            header("Location: movies.php");
            exit();
        }
        // include __DIR__ . '/../views/add_movie.php';
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
