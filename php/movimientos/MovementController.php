<?php 
require_once __DIR__ . '/Movement.php';
require_once __DIR__ . '/../config.php';

class MovementController
{

    private $movementModel;
    public $lastError = ""; //Almacenar el último mensaje de error

    // Constructor
    public function __construct()
    {
        global $pdo;
        $this->movementModel = new Movement($pdo);
    }

    // Mostrar todos los movimientos
    public function index()
    {
        $movements = $this->movementModel->getAllMovements();
        return $movements;
    }

    // Mostrar un movimiento concreto
    public function getMovementById($id)
    {
        return $this->movementModel->getMovementById($id);
    }

    // Añadir nuevo movimiento
    public function addMovement($username, $he_did, $moment, $with_result)
    {
        return $this->movementModel->addMovement($username, $he_did, $moment, $with_result);
    }

    // Eliminar un movimiento
    public function deleteMovement($id)
    {
        return $this->movementModel->deleteMovement($id);
    }

    // Eliminar TODOS los movimientos
    public function deleteAllMovements()
    {
        return $this->movementModel->deleteAllMovements();
    }

}
?>