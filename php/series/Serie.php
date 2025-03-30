<?php
    //traer el archivo de configuración de la base de datos
    require_once __DIR__ . '/../config.php';

    class Serie{
        private $pdo;

        // Constructor
        public function __construct($pdo){
            $this->pdo = $pdo;
        }

        // Obtener todas las series
        public function getAllSeries(){
            $stmt = $this->pdo->query("SELECT * FROM Series");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtener una serie por un ID concreto
        public function getSerieById($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM Movies WHERE id_serie = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Añadir una serie
        public function createSerie($name, $poster, $gender, $languages, $seasons, $complete, $year, $quality, $backup, $rating, $server, $size){
            $stmt = $this->pdo->prepare("INSERT INTO Series (name, poster, gender, languages, seasons, complete, year, quality, backup, rating, server, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$name, $poster, $gender, $languages, $seasons, $complete, $year, $quality, $backup, $rating, $server, $size]);
        }

        // Eliminar una serie
        public function deleteSerie($id) {
            $stmt = $this->pdo->prepare("DELETE FROM Series WHERE id_serie = ?");
            return $stmt->execute([$id]);
        }

        // Actualizar una serie
        public function updateSerie($id, $name, $poster, $gender, $languages, $seasons, $complete, $year, $quality, $backup, $rating, $server, $size) {
            $stmt = $this->pdo->prepare("UPDATE Series SET name=?, poster=?, gender=?, languages=?, seasons=?, complete=?, year=?, quality=?, backup=?, rating=?, server=?, size=? WHERE id_serie=?");
            return $stmt->execute([$name, $poster, $gender, $languages, $seasons, $complete, $year, $quality, $backup, $rating, $server, $size, $id]);
        }
    }
?>