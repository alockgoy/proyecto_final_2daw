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

    // Crear un usuario
    public function createUser($username, $profile, $email, $salt, $password, $two_factor)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Users (username, profile, email, salt, password, two_factor) 
                                         VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $profile, $email, $salt, $password, $two_factor]);
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
}
?>