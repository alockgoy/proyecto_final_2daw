<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

// Traer los archivos necesarios
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Crear instancia del controlador
$userController = new UserController();

if (!isset($_SESSION['email']) && isset($userData['email'])) {
    // Si no existe en la sesión pero sí en los datos del usuario, recuperarlo
    $_SESSION['email'] = $userData['email'];
}

// Obtener datos del usuario
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$targetUsername = urldecode($_GET['username']);
$userData = $userController->getUserByUsername($targetUsername);
$userRol = $userController->getUserRol($username);

// Comprobar que el usuario sea 'root'
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

if (!$userData) {
    die("Error: No se ha podido obtener la información del usuario.");
}


// Procesar cambio de nombre de usuario
if (isset($_POST['update_username'])) {
    $newUsername = trim($_POST['new_username']);

    if (empty($newUsername)) {
        $error = ("El nombre de usuario no puede estar vacío.");
    } elseif ($newUsername === $userData['username']) {
        $error = "El nuevo nombre debe ser diferente al actual.";
    } else {
        $result = $userController->updateUsername($userData['id_user'], $newUsername);

        if ($result) {
            $success = ("Nombre de usuario actualizado correctamente, recargando...");
            //header("Location: my_profile.php");
            //exit();
        } else {
            $error = "No se pudo actualizar el nombre de usuario.";
        }
    }
}

// Procesar cambio de correo electrónico
if (isset($_POST['update_email'])) {
    $newEmail = trim($_POST['new_email']);
    $passwordForEmail = trim($_POST['password_for_email']);

    if (empty($newEmail)) {
        $error = ("El correo electrónico no puede estar vacío.");
    } elseif (empty($passwordForEmail)) {
        $error = ("Debes escribir tu contraseña para realizar cambios.");
    } elseif (!$userController->checkPassword($username, $passwordForEmail)) {
        $error = ("Contraseña incorrecta.");
    } elseif ($newEmail === $email) {
        $error = ("El nuevo correo debe ser diferente al actual.");
    } else {
        $result = $userController->updateEmail($userData['id_user'], $newEmail);

        if ($result) {
            $_SESSION['email'] = $newEmail;
            $success = ("Correo electrónico actualizado correctamente, recargando...");
            //header("Location: my_profile.php");
            //exit();
        } else {
            $error = ("No se pudo actualizar el correo electrónico. Puede ser que ya esté en uso.");
        }
    }
}

// Procesar cambio de contraseña
if (isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Todos los campos de contraseña son obligatorios.";
    } else if ($newPassword !== $confirmPassword) {
        $error = "Las nuevas contraseñas no coinciden.";
    } else {
        $result = $userController->updatePassword($userData['id_user'], $currentPassword, $newPassword);

        if ($result) {
            $success = ("Contraseña actualizada correctamente, recargando...");
        } else {
            $error = ("No se pudo actualizar la contraseña.");
        }
    }
}

// Procesar cambio de verificación en 2 pasos
if (isset($_POST['update_2fa'])) {
    $new2FAStatus = isset($_POST['two_factor']) ? 1 : 0;

    if ($new2FAStatus != $userData['two_factor']) {
        $result = $userController->update2FAStatus($userData['id_user'], $new2FAStatus);

        if ($result) {
            $success = ("Estado de verificación en 2 pasos actualizado correctamente, recargando...");
            $userData['two_factor'] = $new2FAStatus;
        } else {
            $error = ("No se pudo actualizar el estado de verificación en 2 pasos.");
        }
    }
}

// Procesar cambio de foto de perfil
if (isset($_POST["update_pic"])) {
    $result = $userController->updateProfileImage($userData['id_user'], $_FILES['profile_pic'] ?? null);

    if ($result['success']) {
        $message = $result['message'];
        $userData['profile'] = $result['profile'];

        // Opcional: refrescar la página para mostrar la nueva imagen
        header("Location: my_profile.php?updated=profile");
        exit();
    } else {
        $error = $result['message'];
    }
}

// Procesar solicitud para ser admin
if (isset($_POST['ask_for_admin'])) {
    try {
        $result = $userController->askForAdmin($username);
        if ($result) {
            $success = "Solicitud para ser admin enviada correctamente, recargando...";
            $userRol = "solicita"; // Actualizar el rol en la sesión
        } else {
            $error = "No se pudo enviar la solicitud para ser admin.";
        }
    } catch (Exception $e) {
        $error = "Error al procesar la solicitud: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Enlace al css de bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Enlace al archivo css -->
    <link href="../../css/usuarios/my_profile.css" type="text/css" rel="stylesheet" />
    <title>Mi Perfil</title>

    <!-- Para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon" />
</head>

<body>
    <div class="container">
        <div class="main-container">
            <div class="row">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert" data-redirect="./movies.php">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Columna izquierda para la foto de perfil -->
                <div class="col-md-3 border-right">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <img src="<?php echo !empty($userData['profile']) ? '../../' . $userData['profile'] : '../../img/avatares_usuarios/default.jpg'; ?>"
                            alt="Foto de perfil" class="profile-pic rounded-circle mt-5" />

                        <form method="POST" enctype="multipart/form-data" class="mt-3">
                            <div class="form-group">
                                <label for="profile_pic" class="labels">Cambiar foto de perfil:</label>
                                <input type="file" id="profile_pic" name="profile_pic" accept="image/*"
                                    class="form-control" />
                            </div>
                            <button type="submit" name="update_pic" class="profile-button mt-2">Actualizar foto</button>
                        </form>
                    </div>
                </div>

                <!-- Columna central para las configuraciones principales -->
                <div class="col-md-5 border-right">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Configuración del Perfil</h4>
                        </div>

                        <!-- Nombre de usuario -->
                        <div class="profile-section">
                            <h5>Nombre de usuario</h5>
                            <p>Nombre de usuario actual: <strong><?php echo htmlspecialchars($userData['username']); ?></strong></p>

                            <form method="POST">
                                <div class="form-group">
                                    <label for="new_username" class="labels">Nuevo nombre de usuario:</label>
                                    <input type="text" id="new_username" name="new_username" class="form-control"
                                        required />
                                </div>
                                <button type="submit" name="update_username" class="profile-button">Cambiar nombre de
                                    usuario</button>
                            </form>
                        </div>

                        <!-- Correo electrónico -->
                        <div class="profile-section">
                            <h5>Correo electrónico</h5>
                            <p>Correo actual: <strong><?php echo htmlspecialchars($userData['email']); ?></strong></p>

                            <form method="POST">
                                <div class="form-group">
                                    <label for="new_email" class="labels">Nuevo correo electrónico:</label>
                                    <input type="email" id="new_email" name="new_email" class="form-control" required />

                                    <label for="password_for_email" class="labels">Escribe tu contraseña:</label>
                                    <div class="input-group">
                                        <input type="password" name="password_for_email" id="password_for_email"
                                            class="form-control" placeholder="Escribe tu contraseña" required />
                                        <button type="button" onclick="togglePassword('password_for_email')"
                                            class="input-group-text">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" name="update_email" class="profile-button">Cambiar correo
                                    electrónico</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha para configuraciones adicionales -->
                <div class="col-md-4">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Seguridad</h4>
                        </div>

                        <!-- Cambiar contraseña -->
                        <div class="profile-section">
                            <h5>Cambiar contraseña</h5>

                            <form method="POST">
                                <div class="form-group">
                                    <label for="current_password" class="labels">Contraseña actual:</label>
                                    <div class="input-group">
                                        <input type="password" id="current_password" name="current_password"
                                            class="form-control" required />
                                        <button type="button" onclick="togglePassword('current_password')"
                                            class="input-group-text">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password" class="labels">Nueva contraseña:</label>
                                    <div class="input-group">
                                        <input type="password" id="new_password" name="new_password"
                                            class="form-control" required />
                                        <button type="button" onclick="togglePassword('new_password')"
                                            class="input-group-text">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password" class="labels">Confirmar nueva contraseña:</label>
                                    <div class="input-group">
                                        <input type="password" id="confirm_password" name="confirm_password"
                                            class="form-control" required />
                                        <button type="button" onclick="togglePassword('confirm_password')"
                                            class="input-group-text">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" name="update_password" class="profile-button">Cambiar
                                    contraseña</button>
                            </form>
                        </div>

                        <!-- Verificación en 2 pasos -->
                        <div class="profile-section">
                            <h5>Verificación en 2 pasos</h5>
                            <p>Estado actual:
                                <strong><?php echo $userData['two_factor'] ? 'Activado' : 'Desactivado'; ?></strong>
                            </p>

                            <form method="POST" class="two-factor-form">
                                <div
                                    class="form-group d-flex flex-column flex-sm-row  align-items-start align-items-sm-center gap-2">
                                    <label class="switch">
                                        <input type="checkbox" name="two_factor" <?php echo $userData['two_factor'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>

                                </div>
                                <span class="switch-label">
                                    <?php echo $userData['two_factor'] ? 'Desactivar' : 'Activar'; ?> verificación
                                    en 2 pasos
                                </span>

                                <button type="submit" name="update_2fa" class="profile-button mt-3">
                                    Guardar configuración
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enlaces y botones de acción -->
            <div class="row mt-4">
                <div class="col-12">
                    <a href="./users.php" class="btn btn-primary" style="text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Volver atrás
                    </a>

                    <a class="btn btn-danger" href="delete_user.php?id=<?php echo $userData['id_user']; ?>"
                        onclick="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')"
                        style="text-decoration: none;">
                        <i class="fas fa-trash-alt"></i> Borrar cuenta
                    </a>

                    <?php
                    // Comprobar el rol del usuario y mostrar diferentes botones dependiendo del caso
                    if ($userRol != "root" && $userRol != "solicita"): ?>
                        <form method="POST" class="d-inline">
                            <button type="submit" name="ask_for_admin" class="btn btn-info" style="text-decoration: none;">
                                <i class="fas fa-user-shield"></i> Solicitar ser admin
                            </button>
                        </form>
                    <?php elseif ($userRol == "solicita"): ?>
                        <button type="button" class="btn btn-light" style="text-decoration: none;" disabled>
                            <i class="fas fa-user-shield"></i> Solicitud pendiente
                        </button>
                    <?php else: ?>
                        <a class="btn btn-info" href="../../vistas_admin/usuarios/users.php" style="text-decoration: none;">
                            <i class="fas fa-user-shield"></i> Modo admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Atribuciones por iconos -->
            <div class="atribuciones mt-4">
                <h4>Atribuciones</h4>

                <ul>
                    <li><a href="https://www.flaticon.es/iconos-gratis/perfiles-de-usuario"
                            title="perfiles de usuario iconos">Perfiles de usuario iconos creados por yaicon -
                            Flaticon</a>
                    </li>
                    <li><a href="https://www.flaticon.es/iconos-gratis/film-fotografico"
                            title="film fotográfico iconos">Film fotográfico iconos creados por Iconic Panda -
                            Flaticon</a>
                    </li>
                    <li><a href="https://www.flaticon.es/iconos-gratis/serie" title="serie iconos">Serie iconos creados
                            por
                            shmai - Flaticon</a></li>
                    <li><a href="https://www.flaticon.es/iconos-gratis/usuario-seguro"
                            title="usuario seguro iconos">Usuario
                            seguro iconos creados por Muhammad Atif - Flaticon</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al Javascript de mi perfil -->
    <script src="../../js/usuarios/my_profile.js"></script>

    <!-- Enlace al Javascript de ver contraseña -->
    <script src="../../js/usuarios/show_password_my_profile.js"></script>
</body>

</html>