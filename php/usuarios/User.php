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
    public function updateUser($id_user, $username, $profile, $email, $salt, $password, $two_factor)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET username=?, profile=?, email=?, salt=?, password=?, two_factor=? WHERE id_user=?");
        return $stmt->execute([$username, $profile, $email, $salt, $password, $two_factor, $id_user]);
    }

    // Obtener el ID de un usuario que ha iniciado la sesión
    public function getUserIdByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT id_user FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_user'] : null;
    }

    // Iniciar sesión
    public function login($username, $password)
    {
        try {
            // Primero, obtener el usuario por su nombre
            $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false; // Usuario no encontrado
            }

            // Verificar la contraseña usando la salt almacenada
            $hashedPassword = hash('sha256', $password . $user['salt']);

            if ($hashedPassword === $user['password']) {
                // Si la contraseña es correcta, vaciar los datos
                unset($user['password']);
                unset($user['salt']);
                return $user;
            }

            // Contraseña incorrecta
            return false;
        } catch (PDOException $e) {
            error_log("Error en iniciando sesión: " . $e->getMessage());
            return false;
        }
    }

    // Comprobar la contraseña de un usuario
    public function checkPassword($username, $password)
    {
        try {
            // Obtener los datos del usuario
            $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false; // Usuario no encontrado
            }

            // Verificar la contraseña usando la salt almacenada
            $hashedPassword = hash('sha256', $password . $user['salt']);

            if ($hashedPassword === $user['password']) {
                // Si la contraseña es correcta, vaciar los datos
                unset($user['password']);
                unset($user['salt']);
                return $user;
            }

            // Contraseña incorrecta
            return false;
        } catch (PDOException $e) {
            error_log("Error actualizando el dato: " . $e->getMessage());
            return false;
        }
    }

    // Obtener el nombre de usuario que ha iniciado sesión
    public function getUserByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar el nombre de usuario
    public function updateUsername($userId, $newUsername)
    {
        // Verificar que el nuevo nombre de usuario no está repetido
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Users WHERE username = ? AND id_user != ?");
        $stmt->execute([$newUsername, $userId]);
        if ($stmt->fetchColumn() > 0) {
            return false; // El nombre de usuario ya existe
        }

        $stmt = $this->pdo->prepare("UPDATE Users SET username = ? WHERE id_user = ?");
        return $stmt->execute([$newUsername, $userId]);
    }

    // Actualizar el correo electrónico
    public function updateEmail($userId, $newEmail)
    {
        // Verificar que el nuevo correo electrónico no está repetido
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ? AND id_user != ?");
        $stmt->execute([$newEmail, $userId]);
        if ($stmt->fetchColumn() > 0) {
            return false; // El correo electrónico ya existe
        }

        $stmt = $this->pdo->prepare("UPDATE Users SET email = ? WHERE id_user = ?");
        return $stmt->execute([$newEmail, $userId]);
    }

    // Actualizar la contraseña
    public function updatePassword($userId, $currentPassword, $newPassword)
    {
        // Obtener la información actual del usuario
        $stmt = $this->pdo->prepare("SELECT salt, password FROM Users WHERE id_user = ?");
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña actual
        $hashedCurrentPassword = hash('sha256', $currentPassword . $userData['salt']);
        if ($hashedCurrentPassword !== $userData['password']) {
            return false; // Contraseña actual incorrecta
        }

        // Generar un nueva salt y hash para la nueva contraseña
        $newSalt = rand(-1000000, 1000000);
        $hashedNewPassword = hash('sha256', $newPassword . $newSalt);

        // Actualizar la contraseña
        $stmt = $this->pdo->prepare("UPDATE Users SET password = ?, salt = ? WHERE id_user = ?");
        return $stmt->execute([$hashedNewPassword, $newSalt, $userId]);
    }

    // Actualizar el estado de la verificación en 2 pasos
    public function update2FAStatus($userId, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET two_factor = ? WHERE id_user = ?");
        return $stmt->execute([$status, $userId]);
    }

    // Obtener el perfil del usuario
    public function getUserProfilePicture($username)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT profile FROM Users WHERE username = ?");
            $stmt->execute([$username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && !empty($result['profile']) ? $result['profile'] : null;
        } catch (PDOException $e) {
            error_log("Error al obtener imagen de perfil: " . $e->getMessage());
            return null;
        }
    }

    // Actualizar la foto de perfil de un usuario
    public function updateProfilePicture($userId, $profilePath)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET profile = ? WHERE id_user = ?");
        return $stmt->execute([$profilePath, $userId]);
    }

    // Comprobar que un correo existe
    public function checkEmailExists($email)
    {
        $stmt = $this->pdo->prepare("SELECT Email FROM Users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetchColumn() > 0) {
            return $email;
        }
    }

    // Reestablecer la contraseña del usuario
    public function resetUserPassword($email, $salt, $password)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET password = ?, salt = ? WHERE email = ?");
        return $stmt->execute([$password, $salt, $email]);
    }

    // Comprobar si el usuario tiene activada la verificación en 2 pasos
    public function check2FAStatus($username)
    {
        $stmt = $this->pdo->prepare("SELECT two_factor FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetchColumn();

        return $result == '1';
    }

    // Obtener el nombre de usuario en base a su correo
    public function getUsernameByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT username FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comprobar si se encontró un resultado
        return $result ? $result['username'] : null;
    }

    // Obtener el rol de un usuario
    public function getUserRol($username)
    {
        $stmt = $this->pdo->prepare("SELECT rol FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['rol'] : null;
    }  
}
?>