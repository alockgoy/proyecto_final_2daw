<?php 
    require_once __DIR__ . '/User.php';
    require_once __DIR__ . '/../config.php';

    class UserController{
        private $userModel;

        // Constructor
        public function __construct() {
            global $pdo;
            $this->userModel = new User($pdo);
        }

        // Mostrar todos los usuarios
        public function index() {
            $users = $this->userModel->getAllUsers();
            //include __DIR__ . '/../vistas/users.php';
        }

        // Método getPdo
        public function getPdo() {
            global $pdo;
            return $pdo;
        }

        // Añadir un usuario
        public function addUser() {
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
        public function editUser($id) {
            $movie = $this->userModel->getUserById($id);
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->userModel->updateUser($id, $_POST["username"], $_POST["profile"], $_POST["email"], $_POST["salt"], $_POST["password"], $_POST["two_factor"]);
                header("Location: movies.php");
            }
            //include __DIR__ . '/../views/edit_movie.php';
        }

        // Eliminar un usuario
        public function deleteUser($id) {
            $this->userModel->deleteUser($id);
            header("Location: ../../index.html");
        }

        // Obtener el ID de un usuario que ha iniciado la sesión
        public function getUserIdByUsername($username) {
            return $this->userModel->getUserIdByUsername($username);
        }

        // Iniciar sesión
        public function login($username, $password) {
            return $this->userModel->login($username, $password);
        }

        // Obtener el nombre de usuario que ha iniciado sesión
        public function getUserByUsername($username) {
            return $this->userModel->getUserByUsername($username);
        }

        // Actualizar el nombre de usuario
        public function updateUsername($userId, $newUsername) {
            return $this->userModel->updateUsername($userId, $newUsername);
        }

        // Actualizar el correo electrónico
        public function updateEmail($userId, $newEmail) {
            return $this->userModel->updateEmail($userId, $newEmail);
        }

        // Actualizar la contraseña
        public function updatePassword($userId, $currentPassword, $newPassword) {
            return $this->userModel->updatePassword($userId, $currentPassword, $newPassword);
        }

        // Actualizar el estado de la autenticación en 2 pasos
        public function update2FAStatus($userId, $status) {
            return $this->userModel->update2FAStatus($userId, $status);
        }

        // Obtener el perfil del usuario
        public function getUserProfilePicture($username) {
            $profilePath = $this->userModel->getUserProfilePicture($username);
            return $profilePath ? $profilePath : 'img/avatares_usuarios/default.png';
        }
    }
?>