<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Abrir una sesión
session_start();

// Traer los archivos necesarios
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Variable para almacenar los errores
$error = "";

// Comprobar que se ha pulsado el botón de enviar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validación básica
        if (empty($_POST['username']) || empty($_POST['email']) || 
            empty($_POST['password']) || empty($_POST['confirm_password'])) {
            throw new Exception("Todos los campos obligatorios deben estar completos");
        }
        
        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del email no es válido");
        }
        
        // Validar que las contraseñas coincidan
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Las contraseñas no coinciden");
        }
        
        // Inicializar el controlador y procesar el registro
        $controller = new UserController();
        if ($controller->addUser()) {
            // Guardar la información de la sesión
            $_SESSION['username'] = $_POST['username'];

            header("Location: ../peliculas/movies.php");
            exit();
        } else {
            throw new Exception("Error al registrar el usuario.");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h1>Registro de Usuario</h1>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div>
            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div>
            <label for="profile">Imagen de perfil:</label>
            <input type="file" id="profile" name="profile" accept="image/*" required>
        </div>
        
        <div>
            <button type="submit">Registrarse</button>
            <a href="../../index.html">Cancelar</a>
        </div>
    </form>
    
    <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>