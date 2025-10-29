<?php 
require_once __DIR__ . '/Install.php';
require_once __DIR__ . '/../config.php';

class InstallController{
    private $installModel;

    // Constructor
    public function __construct()
    {
        global $pdo;
        $this->installModel = new Install($pdo);
    }

    // Crear la base de datos
    public function createDatabase()
    {
        try {
            $this->installModel->createDatabase();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la base de datos: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla de usuarios
    public function createTableUsers()
    {
        try {
            $this->installModel->createTableUsers();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Users: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla de calidades
    public function createTableQualities()
    {
        try {
            $this->installModel->createTableQualities();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Qualities: " . $e->getMessage());
            return false;
        }
    }

    // Insertar valores por defecto en calidades
    public function insertValuesQualities()
    {
        try {
            $this->installModel->insertValuesQualities();
            return true;
        } catch (Exception $e) {
            error_log("Error insertando valores en Qualities: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla de películas
    public function createTableMovies()
    {
        try {
            $this->installModel->createTableMovies();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Movies: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla de series
    public function createTableSeries()
    {
        try {
            $this->installModel->createTableSeries();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Series: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla Users_Movies
    public function createTableUsersMovies()
    {
        try {
            $this->installModel->createTableUsersMovies();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Users_Movies: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla Users_Series
    public function createTableUsersSeries()
    {
        try {
            $this->installModel->createTableUsersSeries();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Users_Series: " . $e->getMessage());
            return false;
        }
    }

    // Crear la tabla de movimientos
    public function createTableMovements()
    {
        try {
            $this->installModel->createTableMovements();
            return true;
        } catch (Exception $e) {
            error_log("Error creando la tabla Movements: " . $e->getMessage());
            return false;
        }
    }

    // Crear usuario propietario
    public function createOwner($username, $email, $salt, $password, $profile, $two_factor, $rol)
    {
        try {
            return $this->installModel->createOwner($username, $email, $salt, $password, $profile, $two_factor, $rol);
        } catch (Exception $e) {
            error_log("Error creando propietario: " . $e->getMessage());
            return false;
        }
    }
}

?>