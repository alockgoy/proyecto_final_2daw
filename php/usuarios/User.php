<?php
require_once __DIR__ . '/../config.php';

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Obtener todos los usuarios
    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM Users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un usuario por ID concreto
    public function getUserById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si existe un usuario con el mismo nombre o email
    public function checkUserExists($username, $email)
    {
        $stmt = $this->pdo->prepare("SELECT id_user FROM Users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->rowCount() > 0;
    }

    // Crear un usuario
    public function createUser($username, $email, $salt, $password, $profile, $two_factor, $rol)
    {
        try {
            // Comprobar si el usuario ya existe
            if ($this->checkUserExists($username, $email)) {
                return false;
            }
            
            $stmt = $this->pdo->prepare("INSERT INTO Users (username, email, salt, password, profile, two_factor, rol) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$username, $email, $salt, $password, $profile, $two_factor, $rol]);
        } catch (PDOException $e) {
            error_log("Error en createUser: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar un usuario
    public function deleteUser($id_user)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Users WHERE id_user = ?");
        return $stmt->execute([$id_user]);
    }


    // Actualizar un usuario
    public function updateUser($id_user, $username, $profile, $email, $salt, $password, $two_factor) {
        $stmt = $this->pdo->prepare("UPDATE Users SET usernamename=?, profile=?, email=?, salt=?, password=?, two_factor=? WHERE id_user=?");
        return $stmt->execute([$username, $profile, $email, $salt, $password, $two_factor, $id_user]);
    }

    // Obtener el ID de un usuario que ha iniciado la sesión
    public function getUserIdByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT id_user FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_user'] : null;
    }
}
?>