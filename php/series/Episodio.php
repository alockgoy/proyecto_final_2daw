<?php
require_once __DIR__ . '/../config.php';

class Episodio
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Obtener todos los episodios de una serie
    public function getEpisodiosBySerieId($serieId)
    {
        $stmt = $this->pdo->prepare("
            SELECT e.*, q.name AS quality_name
            FROM Episodios e
            JOIN Qualities q ON e.id_quality = q.id_quality
            WHERE e.id_serie = ?
            ORDER BY e.temporada ASC, CAST(e.episodio AS UNSIGNED) ASC
        ");
        $stmt->execute([$serieId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener episodios de una temporada concreta
    public function getEpisodiosByTemporada($serieId, $temporada)
    {
        $stmt = $this->pdo->prepare("
            SELECT e.*, q.name AS quality_name
            FROM Episodios e
            JOIN Qualities q ON e.id_quality = q.id_quality
            WHERE e.id_serie = ? AND e.temporada = ?
            ORDER BY CAST(e.episodio AS UNSIGNED) ASC
        ");
        $stmt->execute([$serieId, $temporada]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un episodio por ID
    public function getEpisodioById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Episodios WHERE id_episodio = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un episodio
    public function createEpisodio($id_serie, $temporada, $episodio, $id_quality, $size, $backup, $server)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Episodios (id_serie, temporada, episodio, id_quality, size, backup, server)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$id_serie, $temporada, $episodio, $id_quality, $size, $backup, $server]);
    }

    // Actualizar un episodio
    public function updateEpisodio($id, $id_quality, $size, $backup, $server)
    {
        $stmt = $this->pdo->prepare("
            UPDATE Episodios SET id_quality=?, size=?, backup=?, server=?
            WHERE id_episodio=?
        ");
        return $stmt->execute([$id_quality, $size, $backup, $server, $id]);
    }

    // Eliminar un episodio
    public function deleteEpisodio($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Episodios WHERE id_episodio = ?");
        return $stmt->execute([$id]);
    }

    // Eliminar todos los episodios de una temporada
    public function deleteEpisodiosByTemporada($serieId, $temporada)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Episodios WHERE id_serie = ? AND temporada = ?");
        return $stmt->execute([$serieId, $temporada]);
    }

    // Comprobar si ya existe un episodio concreto
    public function episodioExists($serieId, $temporada, $episodio)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM Episodios WHERE id_serie = ? AND temporada = ? AND episodio = ?
        ");
        $stmt->execute([$serieId, $temporada, $episodio]);
        return $stmt->fetchColumn() > 0;
    }

    // Obtener el ID del último episodio insertado
    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }

    // Calcular el tamaño total de todos los episodios de una serie
    public function getTotalSizeBySerieId($serieId)
    {
        $stmt = $this->pdo->prepare("SELECT SUM(size) FROM Episodios WHERE id_serie = ?");
        $stmt->execute([$serieId]);
        return (float) $stmt->fetchColumn();
    }
}
?>
