<?php
require_once __DIR__ . '/Episodio.php';
require_once __DIR__ . '/../config.php';

class EpisodioController
{
    private $episodioModel;
    public $lastError = "";

    public function __construct()
    {
        global $pdo;
        $this->episodioModel = new Episodio($pdo);
    }

    // Obtener todos los episodios de una serie
    public function getEpisodiosBySerieId($serieId)
    {
        return $this->episodioModel->getEpisodiosBySerieId($serieId);
    }

    // Obtener episodios de una temporada
    public function getEpisodiosByTemporada($serieId, $temporada)
    {
        return $this->episodioModel->getEpisodiosByTemporada($serieId, $temporada);
    }

    // Obtener un episodio por ID
    public function getEpisodio($id)
    {
        return $this->episodioModel->getEpisodioById($id);
    }

    // Importar todos los episodios de una temporada de golpe
    // $episodios es un array con los datos de cada episodio venidos de TMDB
    // $id_quality, $size, $backup, $server son los valores comunes para toda la temporada
    public function importarTemporada($serieId, $temporada, $episodios, $id_quality, $size, $backup, $server)
    {
        $importados = 0;
        $omitidos = 0;
        $errores = [];

        foreach ($episodios as $ep) {
            $numEpisodio = strval($ep['episode_number']);

            // Saltar si ya existe
            if ($this->episodioModel->episodioExists($serieId, $temporada, $numEpisodio)) {
                $omitidos++;
                continue;
            }

            $result = $this->episodioModel->createEpisodio(
                $serieId,
                $temporada,
                $numEpisodio,
                $id_quality,
                $size,
                $backup ?: null,
                $server
            );

            if ($result) {
                $importados++;
            } else {
                $errores[] = "Error al importar episodio $numEpisodio";
            }
        }

        return [
            'importados' => $importados,
            'omitidos'   => $omitidos,
            'errores'    => $errores
        ];
    }

    // Añadir un episodio individual (episodio doble u otro caso especial)
    public function addEpisodio($serieId, $temporada, $episodio, $id_quality, $size, $backup, $server)
    {
        if ($this->episodioModel->episodioExists($serieId, $temporada, $episodio)) {
            $this->lastError = "El episodio $episodio de la temporada $temporada ya existe.";
            return false;
        }

        return $this->episodioModel->createEpisodio($serieId, $temporada, $episodio, $id_quality, $size, $backup, $server);
    }

    // Actualizar un episodio
    public function updateEpisodio($id, $id_quality, $size, $backup, $server)
    {
        $episodio = $this->episodioModel->getEpisodioById($id);
        if (!$episodio) {
            $this->lastError = "El episodio no existe.";
            return false;
        }
        return $this->episodioModel->updateEpisodio($id, $id_quality, $size, $backup, $server);
    }

    // Eliminar un episodio
    public function deleteEpisodio($id)
    {
        $episodio = $this->episodioModel->getEpisodioById($id);
        if (!$episodio) {
            $this->lastError = "El episodio no existe.";
            return false;
        }
        return $this->episodioModel->deleteEpisodio($id);
    }

    // Eliminar todos los episodios de una temporada
    public function deleteTemporada($serieId, $temporada)
    {
        return $this->episodioModel->deleteEpisodiosByTemporada($serieId, $temporada);
    }

    // Tamaño total de la serie
    public function getTotalSize($serieId)
    {
        return $this->episodioModel->getTotalSizeBySerieId($serieId);
    }

    // Validar datos de episodio
    public function validateEpisodioData($data)
    {
        if (empty($data['id_quality'])) {
            $this->lastError = "La calidad es obligatoria.";
            return false;
        }
        if (!isset($data['size']) || !is_numeric($data['size']) || $data['size'] <= 0) {
            $this->lastError = "El tamaño debe ser un número mayor que 0.";
            return false;
        }
        if (empty($data['server']) || !in_array($data['server'], ['si', 'no'])) {
            $this->lastError = "El valor de servidor no es válido.";
            return false;
        }

        global $pdo;
        $stmt = $pdo->query("SELECT id_quality FROM Qualities");
        $validQualities = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
        if (!in_array($data['id_quality'], $validQualities)) {
            $this->lastError = "La calidad seleccionada no es válida.";
            return false;
        }

        return true;
    }
}
?>
