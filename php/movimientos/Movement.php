<?php 
//traer el archivo de configuración de la base de datos
require_once __DIR__ . '/../config.php';

class Movement
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Obtener todos los movimientos
    public function getAllMovements()
    {
        $stmt = $this->pdo->query("SELECT * FROM Movements ORDER BY moment DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un movimiento concreto
    public function getMovementById($id)
    {
        $stmt = $this->pdo->prepare("SELECT name FROM Movements WHERE id_movement = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Añadir un nuevo movimiento
    public function addMovement($username, $he_did, $moment, $with_result)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Movements (username, he_did, moment, with_result) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $he_did, $moment, $with_result]);
    }

    // Eliminar un movimiento
    public function deleteMovement($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Movements WHERE id_movement = ?");
        return $stmt->execute([$id]);
    }

    // Eliminar TODOS los movimientos
    public function deleteAllMovements()
    {
        $stmt = $this->pdo->query("DELETE FROM Movements");
        return $stmt->rowCount();
    }

}
?>