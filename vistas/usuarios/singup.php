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
        if (
            empty($_POST['username']) || empty($_POST['email']) ||
            empty($_POST['password']) || empty($_POST['confirm_password'])
        ) {
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

        // Validar que el archivo de foto de perfil es una imagen
        if (!empty($_FILES['profile']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $fileType = $_FILES['profile']['type'];

            // Verificar el tipo de imagen
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("El archivo debe ser una imagen (JPEG, PNG o WEBP)");
            }

            // Comprobar algún otro error
            if ($_FILES['profile']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir la imagen");
            }

            // Comprobar que la imagen no supera los 2 MB de peso
            if ($_FILES['profile']['size'] > 2 * 1024 * 1024) {
                throw new Exception("La imagen no debe superar los 2MB");
            }
        }

        // Inicializar el controlador y procesar el registro
        $controller = new UserController();
        if ($controller->addUser()) {
            // Guardar la información de la sesión
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['email'] = $_POST['email'];

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text-css" href="../../css/singup.css" />
    <title>Registro</title>
</head>

<body>
    <section class="min-vh-100 py-5" style="background-color: #f8f9fa;">
        <div class="container py-5">
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="card shadow" style="border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">
                            <div class="row justify-content-center">
                                <div class="col-md-10 col-lg-8">

                                    <h2 class="text-center fw-bold mb-5">Crear cuenta</h2>

                                    <!-- Mensaje de error -->
                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger text-center mb-4" role="alert">
                                            <?php echo htmlspecialchars($error); ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" enctype="multipart/form-data">

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

                                        <!-- Campo del correo electrónico -->
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope fa-lg"></i>
                                            </span>
                                            <div class="form-floating">
                                                <input type="email" id="email" name="email" class="form-control"
                                                    placeholder="Correo electrónico" required />
                                                <label for="email">Correo electrónico</label>
                                            </div>
                                        </div>

                                        <!-- Campo de la contraseña -->
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock fa-lg"></i>
                                            </span>
                                            <div class="form-floating">
                                                <input type="password" id="password" name="password"
                                                    class="form-control" placeholder="Contraseña" required />
                                                <label for="password">Contraseña</label>
                                            </div>
                                            <button type="button" onclick="showPassword()" class="input-group-text">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>

                                        <!-- Campo para repetir la contraseña -->
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-key fa-lg"></i>
                                            </span>
                                            <div class="form-floating">
                                                <input type="password" id="confirm_password" name="confirm_password"
                                                    class="form-control" placeholder="Repite la contraseña" required />
                                                <label for="confirm_password">Repite la contraseña</label>
                                            </div>
                                            <button type="button" onclick="showRepeatPassword()" class="input-group-text">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>

                                        <!-- Campo de la foto de perfil -->
                                        <div class="mb-4">
                                            <label for="profile" class="form-label fw-bold">
                                                <i class="fas fa-image me-2"></i>Foto de perfil (opcional)
                                            </label>
                                            <input type="file" id="profile" name="profile" class="form-control"
                                                accept="image/*" />
                                        </div>

                                        <!-- Botones de acción -->
                                        <div class="d-grid gap-2 mb-4">
                                            <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                                            <a href="../../index.html" class="btn btn-outline-secondary">Cancelar</a>
                                        </div>
                                    </form>

                                    <!-- Enlace al inicio de sesión -->
                                    <div class="text-center mt-4 pt-3 border-top">
                                        <p class="h5 mb-3">¿Ya tienes una cuenta?</p>
                                        <a href="login.php" class="btn btn-success px-4">Iniciar sesión</a>
                                    </div>
                                </div>
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

    <!-- Enlace al archivo JS que permite mostrar la contraseña -->
    <script src="../../js/show_password.js"></script>
</body>

</html>