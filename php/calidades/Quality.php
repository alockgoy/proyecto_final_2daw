<?php
//traer el archivo de configuración de la base de datos
require_once __DIR__ . '/../config.php';

class Quality
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Obtener todas las calidades
    public function getAllQualities()
    {
        $stmt = $this->pdo->query("SELECT * FROM Qualities ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una calidad concreta
    public function getQualityById($id)
    {
        $stmt = $this->pdo->prepare("SELECT name FROM Qualities WHERE id_quality = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Añadir nueva calidad
    public function addQuality($name)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Qualities (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    // Eliminar una calidad
    public function deleteQuality($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Qualities WHERE id_quality = ?");
        return $stmt->execute([$id]);
    }

    // Comprobar si una calidad está en uso en películas
    public function checkMovieQuality($id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Movies WHERE id_quality = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    // Comprobar si una calidad está en uso en series
    public function checkSerieQuality($id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Series WHERE id_quality = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}