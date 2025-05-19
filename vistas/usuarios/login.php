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

            // Comprobar si el usuario tiene habilitada la verificación en 2 pasos
            if ($controller->check2FAStatus($_POST['username'])) {
                
                exit();
            } else {
                // Guardar información en la sesión
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirigir a la página de películas
                header("Location: ../peliculas/movies.php");
                exit();
            }

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Enlace al css de bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous" />

    <!-- Enlace al archivo css -->
    <link rel="stylesheet" type="text-css" href="../../css/login.css" />
    <title>Iniciar Sesión</title>

    <!-- Para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon" />
</head>

<body>
    <section class="min-vh-100 py-5" style="background-color: #f8f9fa;">
        <div class="container py-5">
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow" style="border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">

                            <h2 class="text-center fw-bold mb-5">Iniciar Sesión</h2>

                            <!-- Mensaje de error -->
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger text-center mb-4" role="alert">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <!-- Campo del nombre de usuario -->
                                <div class="input-group mb-4">
                                    <span class="input-group-text">
                                        <i class="fas fa-user fa-lg"></i>
                                    </span>
                                    <div class="form-floating">
                                        <input type="text" id="username" name="username" class="form-control"
                                            placeholder="Nombre de usuario" required />
                                        <label for="username">Nombre de usuario</label>
                                    </div>
                                </div>

                                <!-- Campo de la contraseña -->
                                <div class="input-group mb-4">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock fa-lg"></i>
                                    </span>
                                    <div class="form-floating">
                                        <input type="password" id="password" name="password" class="form-control"
                                            placeholder="Contraseña" required />
                                        <label for="password">Contraseña</label>
                                    </div>
                                </div>

                                <!-- Botón de inicio de sesión -->
                                <div class="d-grid gap-2 mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Iniciar Sesión</button>
                                </div>
                            </form>

                            <!-- Enlaces para registro y volver -->
                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="mb-3">¿No tienes una cuenta? <a href="singup.php" class="fw-bold">Regístrate
                                        aquí</a></p>

                                <!-- Enlace para el archivo de recuperar contraseña -->

                                <p class="mb-3">
                                    <a href="./recover_password.php" class="text-danger">He olvidado mi contraseña</a>
                                </p>
                                <a href="../../index.html" class="btn btn-outline-secondary">Volver al inicio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>

</html>