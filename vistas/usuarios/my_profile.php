<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir al index
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

// Crear instancia del controlador
$userController = new UserController();

// Obtener datos del usuario actual
$username = $_SESSION['username'];
$userData = $userController->getUserByUsername($username);

if (!$userData) {
    die("Error: No se ha podido obtener la información del usuario.");
}


// Procesar cambio de nombre de usuario
if (isset($_POST['update_username'])) {
    $newUsername = trim($_POST['new_username']);

    if (empty($newUsername)) {
        echo ("El nombre de usuario no puede estar vacío.");
    } else if ($newUsername === $username) {
        echo ("El nuevo nombre debe ser diferente al actual.");
    } else {
        $result = $userController->updateUsername($userData['id_user'], $newUsername);

        if ($result) {
            $_SESSION['username'] = $newUsername;
            echo ("Nombre de usuario actualizado correctamente.");
            header("Location: my_profile.php");
            exit();
        } else {
            $error = "No se pudo actualizar el nombre de usuario.";
        }
    }
}

// Procesar cambio de correo electrónico
if (isset($_POST['update_email'])) {
    $newEmail = trim($_POST['new_email']);

    if (empty($newEmail)) {
        echo ("El correo electrónico no puede estar vacío.");
    } else if ($newEmail === $userData['email']) {
        echo ("El nuevo correo debe ser diferente al actual.");
    } else {
        $result = $userController->updateEmail($userData['id_user'], $newEmail);

        if ($result) {
            echo ("Correo electrónico actualizado correctamente.");
            $userData['email'] = $newEmail;
        } else {
            echo ("No se pudo actualizar el correo electrónico.");
        }
    }
}

// Procesar cambio de contraseña
if (isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo ("Todos los campos de contraseña son obligatorios.");
    } else if ($newPassword !== $confirmPassword) {
        echo ("Las nuevas contraseñas no coinciden.");
    } else {
        $result = $userController->updatePassword($userData['id_user'], $currentPassword, $newPassword);

        if ($result) {
            echo ("Contraseña actualizada correctamente.");
        } else {
            echo ("No se pudo actualizar la contraseña.");
        }
    }
}

// Procesar cambio de verificación en 2 pasos
if (isset($_POST['update_2fa'])) {
    $new2FAStatus = isset($_POST['two_factor']) ? 1 : 0;

    if ($new2FAStatus != $userData['two_factor']) {
        $result = $userController->update2FAStatus($userData['id_user'], $new2FAStatus);

        if ($result) {
            echo ("Estado de verificación en 2 pasos actualizado correctamente.");
            $userData['two_factor'] = $new2FAStatus;
        } else {
            echo ("No se pudo actualizar el estado de verificación en 2 pasos.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <title>Mi Perfil</title>
</head>

<body>
    <div class="container">
        <h1>Mi Perfil</h1>

        <!-- Foto de perfil -->
        <div class="profile-section">
            <h2>Foto de perfil</h2>
            <img src="<?php echo !empty($userData['profile']) ? '../../' . $userData['profile'] : '../../uploads/profiles/default.png'; ?>"
                alt="Foto de perfil" class="profile-pic" />

            <form method="POST" action="update_profile_pic.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_pic">Cambiar foto de perfil:</label>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" />
                </div>
                <button type="submit" name="update_pic">Actualizar foto</button>
            </form>
        </div>

        <!-- Nombre de usuario -->
        <div class="profile-section">
            <h2>Nombre de usuario</h2>
            <p>Nombre de usuario actual: <strong><?php echo htmlspecialchars($username); ?></strong></p>

            <form method="POST">
                <div class="form-group">
                    <label for="new_username">Nuevo nombre de usuario:</label>
                    <input type="text" id="new_username" name="new_username" required />
                </div>
                <button type="submit" name="update_username">Cambiar nombre de usuario</button>
            </form>
        </div>

        <!-- Correo electrónico -->
        <div class="profile-section">
            <h2>Correo electrónico</h2>
            <p>Correo actual: <strong><?php echo htmlspecialchars($userData['email']); ?></strong></p>

            <form method="POST">
                <div class="form-group">
                    <label for="new_email">Nuevo correo electrónico:</label>
                    <input type="email" id="new_email" name="new_email" required>
                </div>
                <button type="submit" name="update_email">Cambiar correo electrónico</button>
            </form>
        </div>

        <!-- Cambiar contraseña -->
        <div class="profile-section">
            <h2>Cambiar contraseña</h2>

            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Contraseña actual:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Nueva contraseña:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar nueva contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="update_password">Cambiar contraseña</button>
            </form>
        </div>

        <!-- Verificación en 2 pasos -->
        <div class="profile-section">
            <h2>Verificación en 2 pasos</h2>
            <p>Estado actual: <strong><?php echo $userData['two_factor'] ? 'Activado' : 'Desactivado'; ?></strong></p>

            <form method="POST">
                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" name="two_factor" <?php echo $userData['two_factor'] ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                    <p>Activar/Desactivar verificación en 2 pasos</p>
                </div>

                <button type="submit" name="update_2fa">Guardar configuración</button>
            </form>
        </div>

        <!-- Atribuciones por iconos -->
        <div class="atribuciones">
            <h4>Atribuciones</h4>
            
            <ul>
                <li><a href="https://www.flaticon.es/iconos-gratis/perfiles-de-usuario"
                        title="perfiles de usuario iconos">Perfiles de usuario iconos creados por yaicon - Flaticon</a>
                </li>
                <li><a href="https://www.flaticon.es/iconos-gratis/film-fotografico"
                        title="film fotográfico iconos">Film fotográfico iconos creados por Iconic Panda - Flaticon</a>
                </li>
                <li><a href="https://www.flaticon.es/iconos-gratis/serie" title="serie iconos">Serie iconos creados por
                        shmai - Flaticon</a></li>
                <li><a href="https://www.flaticon.es/iconos-gratis/usuario-seguro" title="usuario seguro iconos">Usuario
                        seguro iconos creados por Muhammad Atif - Flaticon</a></li>
            </ul>
        </div>

        <p>
            <a href="../peliculas/movies.php">Volver a la biblioteca</a>
        </p>

        <a class="btn btn-danger" href="delete_user.php?id=<?php echo $userData['id_user']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')">
            Borrar cuenta
        </a> 
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>