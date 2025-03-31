<?php 
    require_once __DIR__ . '/../models/User.php';
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
            include __DIR__ . '/../vistas/users.php';
        }

        // Añadir un usuario
        public function addUser() {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->userModel->createUser($_POST["username"], $_POST["profile"], $_POST["email"], $_POST["salt"], $_POST["password"], $_POST["two_factor"]);
                header("Location: movies.php");
            }
            include __DIR__ . '/../vistas/add_user.php';
        }

        // Editar un usuario
        public function editUser($id) {
            $movie = $this->userModel->getUserById($id);
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->userModel->updateUser($id, $_POST["username"], $_POST["profile"], $_POST["email"], $_POST["salt"], $_POST["password"], $_POST["two_factor"]);
                header("Location: movies.php");
            }
            include __DIR__ . '/../views/edit_movie.php';
        }

        // Eliminar un usuario
        public function deleteUser($id) {
            $this->userModel->deleteUser($id);
            header("Location: user.php");
        }
    }
?>