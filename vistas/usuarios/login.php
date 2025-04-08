<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Iniciar sesión
session_start();

// Si ya hay una sesión activa, redirigir a la página de películas
if (isset($_SESSION['username'])) {
    header("Location: ../peliculas/movies.php");
    exit();
}

$error = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validación básica
        if (empty($_POST['username']) || empty($_POST['password'])) {
            throw new Exception("El nombre de usuario y la contraseña son obligatorios");
        }
        
        // Inicializar el controlador de usuarios
        $controller = new UserController();
        
        // Intentar iniciar sesión
        $user = $controller->login($_POST['username'], $_POST['password']);
        
        if ($user) {
            // Guardar información en la sesión
            $_SESSION['username'] = $user['username'];
            
            // Redirigir a la página de películas
            header("Location: ../peliculas/movies.php");
            exit();
        } else {
            throw new Exception("Nombre de usuario o contraseña incorrectos");
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
    <title>Iniciar Sesión</title>
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
        
        <p>¿No tienes una cuenta? <a href="singup.php">Regístrate aquí</a></p>
        <p><a href="../../index.html">Volver al inicio</a></p>
    </div>
</body>
</html>