<?php
//traer el archivo de configuración de la base de datos
require_once __DIR__ . '/../config.php';

class Movie {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todas las películas
    public function getAllMovies() {
        $stmt = $this->pdo->query("SELECT * FROM Movies");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una película por ID concreto, necesario para editar
    public function getMovieById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Movies WHERE id_movie = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una película
    public function createMovie($name, $synopsis, $poster, $director, $gender, $languages, $size, $year, $quality, $backup, $server, $rating) {
        $stmt = $this->pdo->prepare("INSERT INTO Movies (name, synopsis, poster, director, gender, languages, size, year, quality, backup, server, rating) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $synopsis, $poster, $director, $gender, $languages, $size, $year, $quality, $backup, $server, $rating]);
    }

    // Actualizar una película
    public function updateMovie($id, $name, $synopsis, $poster, $director, $gender, $languages, $size, $year, $quality, $backup, $server, $rating) {
        $stmt = $this->pdo->prepare("UPDATE Movies SET name=?, synopsis=?, poster=?, director=?, gender=?, languages=?, size=?, year=?, quality=?, backup=?, server=?, rating=? WHERE id_movie=?");
        return $stmt->execute([$name, $synopsis, $poster, $director, $gender, $languages, $size, $year, $quality, $backup, $server, $rating, $id]);
    }

    // Eliminar una película
    public function deleteMovie($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Movies WHERE id_movie = ?");
        return $stmt->execute([$id]);
    }

    // Obtener las películas vinculadas al usuario
    public function getMoviesByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT m.* 
            FROM Movies m
            JOIN Users_Movies um ON m.id_movie = um.id_movie
            WHERE um.id_user = ?
            ORDER BY m.name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Asociar películas con usuarios
    public function associateMovieWithUser($movieId, $userId) {
        $stmt = $this->pdo->prepare("INSERT INTO Users_Movies (id_user, id_movie) VALUES (?, ?)");
        return $stmt->execute([$userId, $movieId]);
    }

    // Obtener el id de la última película añadida
    public function getLastInsertedId() {
        return $this->pdo->lastInsertId();
    }

    // Comprobar que una película está vinculada con un usuario
    public function checkMovieBelongsToUser($movieId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
            SELECT * FROM Users_Movies 
            WHERE id_movie = ? AND id_user = ?
            ");
            $stmt->execute([$movieId, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al borrar la película: " . $e->getMessage());
            return false;
        }
    }
}
?>
