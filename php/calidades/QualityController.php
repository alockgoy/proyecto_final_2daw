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

}
?>