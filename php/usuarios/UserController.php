<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/../config.php';

class UserController
{
    private $userModel;

    // Constructor
    public function __construct()
    {
        global $pdo;
        $this->userModel = new User($pdo);
    }

    // Mostrar todos los usuarios
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        //include __DIR__ . '/../vistas/users.php';
    }

    // Método getPdo
    public function getPdo()
    {
        global $pdo;
        return $pdo;
    }

    // Añadir un usuario
    public function addUser()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Verificar que las contraseñas coincidan
            if ($_POST['password'] !== $_POST['confirm_password']) {
                throw new Exception("Las contraseñas no coinciden");
            }

            // Generar salt aleatoria
            $salt = rand(-1000000, 1000000);

            // Hashear la contraseña con la salt
            $hashedPassword = hash('sha256', $_POST['password'] . $salt);

            // Procesar la imagen de perfil si existe
            $profilePath = null;
            if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
                $targetDir = __DIR__ . "/../../img/avatares_usuarios/";

                // Verificar que el directorio existe, si no, crearlo
                if (!is_dir($targetDir)) {
                    if (!mkdir($targetDir, 0755, true)) {
                        throw new Exception("No se pudo crear el directorio para guardar las imágenes");
                    }
                }

                $fileName = time() . '_' . basename($_FILES["profile"]["name"]);
                $targetFilePath = $targetDir . $fileName;

                // Verificar que el tamaño del archivo no exceda 2 MB
                if ($_FILES["profile"]["size"] > 2 * 1024 * 1024) {
                    throw new Exception("La imagen de perfil no puede pesar más de 2 MB.");
                }

                // Verificar que sea una imagen válida
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');

                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Subir el archivo
                    if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetFilePath)) {
                        $profilePath = "img/avatares_usuarios/" . $fileName;
                    } else {
                        throw new Exception("Error al subir la imagen de perfil.");
                    }
                } else {
                    throw new Exception("Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP para la imagen de perfil.");
                }
            }

            // Determinar si se ha activado la autenticación de dos factores
            $twoFactor = isset($_POST['two_factor']) ? 1 : 0;

            // El rol será siempre 'normal' para nuevos registros
            $rol = 'normal';

            // Crear el usuario
            $result = $this->userModel->createUser(
                $_POST["username"],
                $_POST["email"],
                $salt,
                $hashedPassword,
                $profilePath,
                $twoFactor,
                $rol
            );

            if (!$result) {
                throw new Exception("Error al crear el usuario. El nombre de usuario o email podría ya existir.");
            }

            return true;
        }
        return false;
    }

    // Editar un usuario
    public function editUser($id)
    {
        $movie = $this->userModel->getUserById($id);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->userModel->updateUser($id, $_POST["username"], $_POST["profile"], $_POST["email"], $_POST["salt"], $_POST["password"], $_POST["two_factor"]);
            header("Location: movies.php");
        }
    }


    // Eliminar un usuario
    public function deleteUser($id)
    {
        // Primero obtener la información del usuario para saber su foto de perfil
        $user = $this->userModel->getUserById($id);

        if ($user && !empty($user['profile'])) {
            // Construir la ruta completa a la imagen
            $profilePath = __DIR__ . '/../../' . $user['profile'];

            // Verificar si existe y borrarla
            if (file_exists($profilePath)) {
                if (unlink($profilePath)) {
                    error_log("Foto de perfil eliminada exitosamente: " . $profilePath);
                } else {
                    error_log("Error al eliminar la foto de perfil: " . $profilePath);
                }
            } else {
                error_log("La foto de perfil no existe en el servidor: " . $profilePath);
            }
        }

        // Después, eliminar el usuario de la base de datos
        $this->userModel->deleteUser($id);

        header("Location: ../../index.html");
    }

    // Obtener el ID de un usuario que ha iniciado la sesión
    public function getUserIdByUsername($username)
    {
        return $this->userModel->getUserIdByUsername($username);
    }

    // Iniciar sesión
    public function login($username, $password)
    {
        return $this->userModel->login($username, $password);
    }

    // Obtener el nombre de usuario que ha iniciado sesión
    public function getUserByUsername($username)
    {
        return $this->userModel->getUserByUsername($username);
    }

    // Actualizar el nombre de usuario
    public function updateUsername($userId, $newUsername)
    {
        return $this->userModel->updateUsername($userId, $newUsername);
    }

    // Actualizar el correo electrónico
    public function updateEmail($userId, $newEmail)
    {
        return $this->userModel->updateEmail($userId, $newEmail);
    }

    // Actualizar la contraseña
    public function updatePassword($userId, $currentPassword, $newPassword)
    {
        return $this->userModel->updatePassword($userId, $currentPassword, $newPassword);
    }

    // Actualizar el estado de la autenticación en 2 pasos
    public function update2FAStatus($userId, $status)
    {
        return $this->userModel->update2FAStatus($userId, $status);
    }

    // Obtener el perfil del usuario
    public function getUserProfilePicture($username)
    {
        $profilePath = $this->userModel->getUserProfilePicture($username);
        return $profilePath ? $profilePath : 'img/avatares_usuarios/default.png';
    }

    // Actualizar la foto de perfil de un usuario
    public function updateProfileImage($userId, $file)
    {
        $result = ['success' => false, 'message' => ''];

        try {
            // Validaciones de archivo
            $maxSize = 2 * 1024 * 1024; // 2MB
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            // Verificar si se ha enviado un archivo
            if (!isset($file) || $file['error'] !== 0) {
                throw new Exception("No se ha seleccionado ningún archivo o ha ocurrido un error en la subida.");
            }

            // Verificar tamaño del archivo
            if ($file['size'] > $maxSize) {
                throw new Exception("El archivo es demasiado grande. El tamaño máximo permitido es 2MB.");
            }

            // Verificar tipo de archivo
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Tipo de archivo no permitido. Solo se aceptan imágenes.");
            }

            // Verificar extensión
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Extensión de archivo no permitida. Solo se aceptan .jpg, .jpeg, .png o .webp.");
            }

            // Obtener información actual del usuario para la imagen anterior
            $userData = $this->userModel->getUserById($userId);

            // Crear directorio si no existe
            $targetDir = __DIR__ . "/../../img/avatares_usuarios/";
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0755, true)) {
                    throw new Exception("No se pudo crear el directorio para guardar las imágenes");
                }
            }

            // Generar nombre único para el archivo
            $fileName = time() . '_' . basename($file["name"]);
            $targetFilePath = $targetDir . $fileName;

            // Eliminar foto anterior si existe
            if ($userData && !empty($userData['profile'])) {
                $oldProfilePath = __DIR__ . '/../../' . $userData['profile'];
                if (file_exists($oldProfilePath)) {
                    if (!unlink($oldProfilePath)) {
                        error_log("Error al eliminar el archivo anterior: " . $oldProfilePath);
                    }
                }
            }

            // Mover el archivo subido
            if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                throw new Exception("Error al subir la imagen. Por favor, inténtalo de nuevo.");
            }

            // Actualizar la ruta en la base de datos
            $profilePath = "img/avatares_usuarios/" . $fileName;
            $updateResult = $this->userModel->updateProfilePicture($userId, $profilePath);

            if (!$updateResult) {
                throw new Exception("Error al actualizar la foto de perfil en la base de datos.");
            }

            $result['success'] = true;
            $result['message'] = "Foto de perfil actualizada correctamente.";
            $result['profile'] = $profilePath;

        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }

        return $result;
    }
}
?>