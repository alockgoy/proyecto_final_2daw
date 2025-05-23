<?php
require_once __DIR__ . '/Movie.php';
require_once __DIR__ . '/../config.php';

class MovieController
{
    private $movieModel;
    public $lastError = ""; //Almacenar el último mensaje de error

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

        // Validar datos
        $validation = $this->validateMovieData($_POST, $_FILES);

        if (!$validation['valid']) {
            $this->lastError = $validation['message'];
            return false;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $posterPath = '';

            // Procesar la imagen si ha sido subida
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] == UPLOAD_ERR_OK) {
                $targetDir = __DIR__ . "/../../img/portadas_peliculas/";
                $fileName = basename($_FILES["poster"]["name"]);
                $targetFilePath = $targetDir . $fileName;

                // Eliminar espacios en blanco en el nombre del archivo del poster
                $fileName = str_replace(' ', '_', $fileName);

                // Crear un nombre único para el poster
                $nombreUnicoArchivo = uniqid("poster_") . "_" . basename($_FILES['poster']['name']);
                $rutaPoster = $targetDir . $nombreUnicoArchivo;

                // Comprobar que el archivo fue subido correctamente
                if (!is_uploaded_file($_FILES['poster']['tmp_name'])) {
                    throw new Exception("El archivo subido no es válido o no existe.");
                }

                // Comprobar que el archivo no está vacío
                if ($_FILES['poster']['size'] <= 0) {
                    throw new Exception("El archivo subido está vacío.");
                }

                // Comprobar la extensión del archivo antes de usar getimagesize()
                $fileType = pathinfo($_FILES["poster"]["name"], PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'webp');
                if (!in_array(strtolower($fileType), $allowTypes)) {
                    throw new Exception("Solo se permiten archivos con extensiones JPG, JPEG, PNG y WEBP.");
                }

                // Comprobar que es una imagen real
                $imageInfo = getimagesize($_FILES["poster"]["tmp_name"]);
                if ($imageInfo === false) {
                    throw new Exception("El archivo no es una imagen válida");
                }

                // Verificar MIME type real del archivo
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                if (!in_array($imageInfo['mime'], $allowedMimes)) {
                    throw new Exception("Tipo de imagen no permitido");
                }

                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Subir el archivo
                    if (move_uploaded_file($_FILES["poster"]["tmp_name"], $rutaPoster)) {
                        $posterPath = "img/portadas_peliculas/" . $nombreUnicoArchivo;
                    } else {
                        throw new Exception("Error al subir el archivo de imagen.");
                    }
                } else {
                    throw new Exception("Solo se permiten archivos JPG, JPEG, PNG y WEBP.");
                }
            } else {
                throw new Exception("Debes seleccionar una imagen para el póster.");
            }

            // Parchear la valoración (sí, aquí también)
            $rating = isset($_POST["rating"]) && $_POST["rating"] !== '' ? (int) $_POST["rating"] : null;

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
                $rating
            );

            // Si se ha producido algún error, borrar el poster en caso de haberse subido
            if (!$result && !empty($posterPath)) {
                $fullPath = __DIR__ . '/../../' . $posterPath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            return $result;
        }
        return false;
    }

    // Editar una película
    public function updateMovie($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Validar datos
            $validation = $this->validateMovieDataForEdit($_POST, $_FILES);

            if (!$validation['valid']) {
                $this->lastError = $validation['message'];
                return false;
            }

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

                // Eliminar espacios en blanco en el nombre del archivo del poster
                $fileName = str_replace(' ', '_', $fileName);

                // Crear un nombre único para el poster
                $nombreUnicoArchivo = uniqid("poster_") . "_" . basename($_FILES['poster']['name']);
                $rutaPoster = $targetDir . $nombreUnicoArchivo;

                // Verificar que sea una imagen válida
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'webp');

                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Subir el archivo
                    if (move_uploaded_file($_FILES["poster"]["tmp_name"], $rutaPoster)) {
                        // Borrar el archivo anterior si existe y es diferente
                        if ($currentMovie['poster']) {
                            $oldPosterPath = __DIR__ . "/../../" . $currentMovie['poster'];
                            if (file_exists($oldPosterPath)) {
                                unlink($oldPosterPath);
                            }
                        }

                        $posterPath = "img/portadas_peliculas/" . $nombreUnicoArchivo;
                    } else {
                        throw new Exception("Error al subir el archivo de imagen.");
                    }
                } else {
                    throw new Exception("Solo se permiten archivos JPG, JPEG, PNG y WEBP.");
                }
            }

            // Parchear la puntuación (sí, aquí también)
            $rating = isset($_POST["rating"]) && $_POST["rating"] !== '' ? (int) $_POST["rating"] : null;

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
                $rating
            );

            // Lo mismo que en añadir
            if (!$result && $posterPath !== $currentMovie['poster']) {
                $fullPath = __DIR__ . '/../../' . $posterPath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

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
    public function deleteMoviesWithoutUsers()
    {
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

    // Función para comprobar que los "value" de los formularios no han sido alterados
    public function validateMovieData($data, $files)
    {
        $result = ['valid' => true, 'message' => ''];

        // Campos obligatorios
        $requiredFields = ['name', 'year', 'director', 'gender', 'languages', 'quality', 'size', 'server'];

        // Comprobar campos obligatorios
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return ['valid' => false, 'message' => "Te has dejado vacío un campo obligatorio."];
            }
        }

        // Comprobar que el año es un número
        if (isset($data['year']) && !is_numeric($data['year'])) {
            return ['valid' => false, 'message' => 'El año debe ser un número'];
        }

        // Comprobar archivo
        if (!isset($files['poster']) || $files['poster']['error'] !== 0) {
            return ['valid' => false, 'message' => 'El póster de la película es obligatorio'];
        }

        // Comprobar que el poster no pesa más de 3 MB
        if ($_FILES['poster']['size'] > 3 * 1024 * 1024) { // 3MB en bytes
            return ['valid' => false, 'message' => 'El póster de la película no puede pesar más de 3 MB'];
        }

        // Comprobar puntuación
        if (isset($data['rating']) && $data['rating'] !== '') {

            $rating = trim($data['rating']);

            // Comprobar que sea un número
            if (!is_numeric($rating)) {
                return ['valid' => false, 'message' => 'La calificación debe ser un número'];
            }

            // Verificar si contiene un punto decimal
            if (strpos($rating, '.') !== false) {
                return ['valid' => false, 'message' => 'La calificación no puede ser decimal'];
            }

            // Convertir a entero para la verificación de rango
            $intVal = (int) $rating;

            // Comprobar rango 1-10
            if ($intVal < 1 || $intVal > 10) {
                return ['valid' => false, 'message' => 'La calificación debe estar entre 1 y 10'];
            }
        }


        // Comprobar opciones de los géneros
        $validGenders = [
            'acción/aventura',
            'animación',
            'anime',
            'ciencia ficción',
            'cortometraje',
            'comedia',
            'deportes',
            'documental',
            'drama',
            'familiar',
            'fantasía',
            'guerra',
            'terror',
            'musical',
            'suspense',
            'romance',
            'vaqueros',
            'misterio'
        ];

        // Verificar que no se ha cambiado el valor de un género
        if (!in_array($data['gender'], $validGenders)) {
            return ['valid' => false, 'message' => 'El género seleccionado no es válido'];
        }

        // Comprobar opciones de las calidades
        $validQualities = ['4K', '1440p', '1080p', '720p', '420p', 'otro'];

        // Verificar que no se ha cambiado el valor de una calidad
        if (!in_array($data['quality'], $validQualities)) {
            return ['valid' => false, 'message' => 'La calidad seleccionada no es válida'];
        }

        // Comprobar que el tamaño es un número
        if (isset($data['size']) && !is_numeric($data['size'])) {
            return ['valid' => false, 'message' => 'El tamaño debe ser un número'];
        }

        // Comprobar opciones de servidor
        $validServerOptions = ['si', 'no'];

        // Verificar que no se ha cambiado el valor
        if (!in_array($data['server'], $validServerOptions)) {
            return ['valid' => false, 'message' => 'El valor para "En servidor" no es válido'];
        }

        return $result;
    }

    // Lo mismo de antes pero para la edición de películas, que cambia alguna cosa
    public function validateMovieDataForEdit($data, $files)
    {
        $result = ['valid' => true, 'message' => ''];

        // Campos obligatorios
        $requiredFields = ['name', 'year', 'director', 'gender', 'languages', 'quality', 'size', 'server'];

        // Comprobar campos obligatorios
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return ['valid' => false, 'message' => "Te has dejado vacío un campo obligatorio."];
            }
        }

        // Comprobar que el año es un número
        if (isset($data['year']) && !is_numeric($data['year'])) {
            return ['valid' => false, 'message' => 'El año debe ser un número'];
        }

        // Comprobar puntuación
        if (isset($data['rating']) && $data['rating'] !== '') {

            $rating = trim($data['rating']);

            // Comprobar que sea un número
            if (!is_numeric($rating)) {
                return ['valid' => false, 'message' => 'La calificación debe ser un número'];
            }

            // Verificar si contiene un punto decimal
            if (strpos($rating, '.') !== false) {
                return ['valid' => false, 'message' => 'La calificación no puede ser decimal'];
            }

            // Convertir a entero para la verificación de rango
            $intVal = (int) $rating;

            // Comprobar rango 1-10
            if ($intVal < 1 || $intVal > 10) {
                return ['valid' => false, 'message' => 'La calificación debe estar entre 1 y 10'];
            }
        }

        // Comprobar opciones de los géneros
        $validGenders = [
            'acción/aventura',
            'animación',
            'anime',
            'ciencia ficción',
            'cortometraje',
            'comedia',
            'deportes',
            'documental',
            'drama',
            'familiar',
            'fantasía',
            'guerra',
            'terror',
            'musical',
            'suspense',
            'romance',
            'vaqueros',
            'misterio'
        ];

        // Verificar que no se ha cambiado el valor de un género
        if (!in_array($data['gender'], $validGenders)) {
            return ['valid' => false, 'message' => 'El género seleccionado no es válido'];
        }

        // Comprobar opciones de las calidades
        $validQualities = ['4K', '1440p', '1080p', '720p', '420p', 'otro'];

        // Verificar que no se ha cambiado el valor de una calidad
        if (!in_array($data['quality'], $validQualities)) {
            return ['valid' => false, 'message' => 'La calidad seleccionada no es válida'];
        }

        // Comprobar que el tamaño es un número
        if (isset($data['size']) && !is_numeric($data['size'])) {
            return ['valid' => false, 'message' => 'El tamaño debe ser un número'];
        }

        // Comprobar opciones de servidor
        $validServerOptions = ['si', 'no'];

        // Verificar que no se ha cambiado el valor
        if (!in_array($data['server'], $validServerOptions)) {
            return ['valid' => false, 'message' => 'El valor para "En servidor" no es válido'];
        }

        // Validación de la imagen (opcional en edición)
        if (isset($files['poster']) && $files['poster']['error'] === 0) {
            // Comprobar que el poster no pesa más de 3 MB
            if ($files['poster']['size'] > 3 * 1024 * 1024) { // 3MB en bytes
                return ['valid' => false, 'message' => 'El póster de la película no puede pesar más de 3 MB'];
            }
        }

        return $result;
    }
}
?>