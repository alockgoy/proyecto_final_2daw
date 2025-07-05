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
        $stmt = $this->pdo->query("SELECT * FROM Qualities");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una calidad concreta
    public function getQualityById($id)
    {
        $stmt = $this->pdo->prepare("SELECT name FROM Qualities WHERE id_quality = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}