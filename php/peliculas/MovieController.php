<?php
require_once __DIR__ . '/Movie.php';
require_once __DIR__ . '/../config.php';

class MovieController
{
    private $movieModel;

    // Constructor
    public function __construct()
    {
        global $pdo;
        $this->movieModel = new Movie($pdo);
    }

    // Mostrar todas las películas
    public function index()
    {
        $movies = $this->movieModel->getAllMovies();
        return $movies;
        //include __DIR__ . '/../views/movies.php';
    }

    // Añadir una película
    public function addMovie()
    {
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
                    echo ("El archivo de imagen no puede pesar más de 5 MB.");
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
            $result = $this->movieModel->createMovie(
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

            return $result;
        }
        return false;
    }

    // Editar una película
    public function updateMovie($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $posterPath = null;

            // Obtener la película actual para saber la ruta de la imagen actual
            $currentMovie = $this->movieModel->getMovieById($id);

            if (!$currentMovie) {
                throw new Exception("La película no existe");
            }

            // Usar la ruta de la imagen actual si no se está subiendo una nueva
            $posterPath = $currentMovie['poster'];

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
                    throw new Exception("El archivo de imagen no puede pesar más de 5 MB.");
                }

                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Subir el archivo
                    if (move_uploaded_file($_FILES["poster"]["tmp_name"], $targetFilePath)) {
                        // Borrar el archivo anterior si existe y es diferente
                        if ($currentMovie['poster'] && $currentMovie['poster'] != "img/portadas_peliculas/" . $fileName) {
                            $oldPosterPath = __DIR__ . "/../../" . $currentMovie['poster'];
                            if (file_exists($oldPosterPath)) {
                                unlink($oldPosterPath);
                            }
                        }

                        $posterPath = "img/portadas_peliculas/" . $fileName;
                    } else {
                        throw new Exception("Error al subir el archivo de imagen.");
                    }
                } else {
                    throw new Exception("Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.");
                }
            }

            // Actualizar la película en la base de datos
            $result = $this->movieModel->updateMovie(
                $id,
                $_POST["name"],
                $_POST["synopsis"],
                $posterPath,
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

            if (!$result) {
                throw new Exception("No se pudo actualizar la película");
            }

            return true;
        }
        return false;
    }

    // Eliminar una película
    public function deleteMovie($id)
    {

        // Comprobar que la película existe antes de eliminarla
        $movie = $this->movieModel->getMovieById($id);
        if (!$movie) {
            throw new Exception("La película no existe");
        }

        // Si la película tiene un póster, eliminarlo del sistema de archivos
        if (!empty($movie['poster'])) {
            $posterPath = __DIR__ . '/../../' . $movie['poster'];
            if (file_exists($posterPath)) {
                unlink($posterPath);
            }
        }

        $this->movieModel->deleteMovie($id);
        header("Location: movies.php");
    }

    // Obtener una película concreta
    public function getMovie($id)
    {
        return $this->movieModel->getMovieById($id);
    }

    // Obtener las películas vinculadas al usuario
    public function getMoviesByUserId($userId)
    {
        return $this->movieModel->getMoviesByUserId($userId);
    }

    // Asociar películas con usuarios
    public function associateMovieWithUser($movieId, $userId)
    {
        return $this->movieModel->associateMovieWithUser($movieId, $userId);
    }

    // Obtener el ID de la última película añadida
    public function getLastInsertedId()
    {
        return $this->movieModel->getLastInsertedId();
    }

    // Comprobar que una película está vinculada con un usuario
    public function checkMovieBelongsToUser($movieId, $userId)
    {
        return $this->movieModel->checkMovieBelongsToUser($movieId, $userId);
    }

    // Borrar películas que no tienen usuario asociado
    public function deleteMoviesWithoutUsers(){
        // Obtener películas sin usuario
        $movies = $this->movieModel->getMoviesWithoutUser();

        // Eliminar pósters antes de eliminar los registros
        foreach ($movies as $movie) {
            if (!empty($movie['poster'])) {
                $posterPath = __DIR__ . '/../../' . $movie['poster'];

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
        $result = $this->movieModel->deleteMoviesWithoutUsers();

        error_log("Resultado de eliminar películas: " . ($result ? "Éxito" : "Fallo"));
        return $result;
    }
}
?>