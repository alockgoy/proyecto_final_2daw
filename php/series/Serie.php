<?php
//traer el archivo de configuración de la base de datos
require_once __DIR__ . '/../config.php';

class Serie
{
    private $pdo;

    // Constructor
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Obtener todas las series
    public function getAllSeries()
    {
        $stmt = $this->pdo->query("SELECT * FROM Series");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una serie por un ID concreto
    public function getSerieById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Series WHERE id_serie = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Añadir una serie
    public function createSerie($name, $poster, $gender, $languages, $seasons, $complete, $year, $id_quality, $backup, $rating, $server, $size)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Series (name, poster, gender, languages, seasons, complete, year, id_quality, backup, rating, server, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $poster, $gender, $languages, $seasons, $complete, $year, $id_quality, $backup, $rating, $server, $size]);
    }

    // Eliminar una serie
    public function deleteSerie($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Series WHERE id_serie = ?");
        return $stmt->execute([$id]);
    }

    // Actualizar una serie
    public function updateSerie($id, $name, $poster, $gender, $languages, $seasons, $complete, $year, $id_quality, $backup, $rating, $server, $size)
    {
        $stmt = $this->pdo->prepare("UPDATE Series SET name=?, poster=?, gender=?, languages=?, seasons=?, complete=?, year=?, id_quality=?, backup=?, rating=?, server=?, size=? WHERE id_serie=?");
        return $stmt->execute([$name, $poster, $gender, $languages, $seasons, $complete, $year, $id_quality, $backup, $rating, $server, $size, $id]);
    }

    // Obtener las series vinculadas al usuario
    public function getSeriesByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT s.* 
            FROM Series s
            JOIN Users_Series us ON s.id_serie = us.id_serie
            WHERE us.id_user = ?
            ORDER BY s.name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Comprobar que una serie está vinculada con un usuario
    public function checkSerieBelongsToUser($serieId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
            SELECT * FROM Users_Series 
            WHERE id_serie = ? AND id_user = ?
            ");
            $stmt->execute([$serieId, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al borrar la serie: " . $e->getMessage());
            return false;
        }
    }

    // Obtener el id de la última serie añadida
    public function getLastInsertedId() {
        return $this->pdo->lastInsertId();
    }

    // Asociar series con usuarios
    public function associateSerieWithUser($serieId, $userId) {
        $stmt = $this->pdo->prepare("INSERT INTO Users_Series (id_user, id_serie) VALUES (?, ?)");
        return $stmt->execute([$userId, $serieId]);
    }

    // Obtener series que no tienen usuarios asociados (rezagadas en la tabla Series)
    public function getSeriesWithoutUser()
    {
        $stmt = $this->pdo->query("
            SELECT s.* 
            FROM Series s
            LEFT JOIN Users_Series us ON s.id_serie = us.id_serie
            WHERE us.id_serie IS NULL
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Borrar series que no tienen usuario asociado
    public function deleteSeriesWithoutUsers()
    {
        $stmt = $this->pdo->prepare("
        DELETE FROM Series 
        WHERE id_serie NOT IN (SELECT id_serie FROM Users_Series)
    ");
        return $stmt->execute();
    }

    // Obtener el nombre de usuario que subió la serie
    public function getUserNameBySerieId($serieId)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.username 
            FROM Users u
            JOIN Users_Series us ON u.id_user = us.id_user
            WHERE us.id_serie = ?
        ");
        $stmt->execute([$serieId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['username'] : null;
    }
}
?>