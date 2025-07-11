<?php
require_once __DIR__ . '/Quality.php';
require_once __DIR__ . '/../config.php';

class QualityController
{

    private $qualityModel;
    public $lastError = ""; //Almacenar el último mensaje de error

    // Constructor
    public function __construct()
    {
        global $pdo;
        $this->qualityModel = new Quality($pdo);
    }

    // Mostrar todas las calidades
    public function index()
    {
        $qualities = $this->qualityModel->getAllQualities();
        return $qualities;
    }

    // Mostrar una calidad concreta
    public function getQualityById($id)
    {
        return $this->qualityModel->getQualityById($id);
    }

    // Añadir nueva calidad
    public function addQuality($name)
    {
        return $this->qualityModel->addQuality($name);
    }

    // Eliminar una calidad
    public function deleteQuality($id)
    {
        return $this->qualityModel->deleteQuality($id);
    }

    // Comprobar si una calidad está en uso en películas
    public function checkMovieQuality($id)
    {
        return $this->qualityModel->checkMovieQuality($id);
    }

    // Comprobar si una calidad está en uso en series
    public function checkSerieQuality($id)
    {
        return $this->qualityModel->checkSerieQuality($id);
    }
}
?>