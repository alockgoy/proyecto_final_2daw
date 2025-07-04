<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon" />
    <link rel="stylesheet" type="text-css" href="../../css/usuarios/singup.css" />
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
                                            <button type="button" onclick="showRepeatPassword()"
                                                class="input-group-text">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>

                                        <!-- Campo de la foto de perfil -->
                                        <div class="mb-4">
                                            <label for="profile" class="form-label fw-bold">
                                                <i class="fas fa-image me-2"></i>Foto de perfil (opcional)
                                            </label>
                                            <div class="d-flex align-items-center">
                                                <input type="file" id="profile" name="profile" class="form-control"
                                                    accept="image/*" />
                                                <a href="#" id="clear" class="btn btn-outline-danger ms-2"
                                                    title="Eliminar foto de perfil">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
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

                                    <!-- Enlace a la documentación -->
                                    <div class="text-center mt-4 pt-3 border-top">
                                        <p class="">Es recomendable leer el &nbsp;
                                            <a href="../../html/manual.html" target="_blank" class="link">manual de
                                                uso</a>
                                        </p>
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
    <script src="../../js/usuarios/show_password.js"></script>

    <!-- Enlace al archivo JS que permite limpiar el archivo del formulario -->
    <script src="../../js/usuarios/delete_input_file.js"></script>
</body>

</html>