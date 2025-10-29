<?php
session_start();

// Traer los archivos necesarios
require_once '../../php/instalacion/InstallController.php';
require_once '../../php/instalacion/Install.php';
require_once '../../php/config.php';

// Inicializar el controlador
$controller = new InstallController();

// Si ya existe la BD, redirigir al index
try {
    $stmt = $pdo->query("USE BibliotecaMultimedia");
    header('Location: ../../index.html');
    exit();
} catch (PDOException $e) {
    // La BD no existe, continuar con la instalación
}

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $formusername = trim($_POST['username'] ?? '');
    $formemail = trim($_POST['email'] ?? '');
    $formpassword = $_POST['password'] ?? '';
    $formconfirm_password = $_POST['confirm_password'] ?? '';
    
    // Variable para almacenar errores
    $error = null;
    
    // Validaciones
    if (empty($formusername) || empty($formemail) || empty($formpassword) || empty($formconfirm_password)) {
        $error = 'No puede haber campos vacíos';
    } elseif (!filter_var($formemail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Correo inválido';
    } elseif ($formpassword !== $formconfirm_password) {
        $error = 'Las contraseñas no coinciden';
    }
    
    // Si no hay errores, proceder con la instalación
    if ($error === null) {
        try {
            // 1. Crear la base de datos
            $controller->createDatabase();
            
            // 2. Obtener las variables de configuración del config.php original
            global $host, $dbname, $username, $password;
            
            // 3. Reconectar a la base de datos recién creada usando las variables de config.php
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 4. Recrear el controlador con la nueva conexión
            $controller = new InstallController();
            
            // 5. Crear todas las tablas en orden
            $controller->createTableUsers();
            $controller->createTableQualities();
            $controller->insertValuesQualities();
            $controller->createTableMovies();
            $controller->createTableSeries();
            $controller->createTableUsersMovies();
            $controller->createTableUsersSeries();
            $controller->createTableMovements();
            
            // 6. Crear el usuario propietario
            $salt = rand(-1000000, 1000000);
            $hashedPassword = hash('sha256', $formpassword . $salt);
            $profile = 'img/avatares_usuarios/default.jpg'; // Imagen por defecto
            
            $result = $controller->createOwner(
                $formusername,
                $formemail,
                $salt,
                $hashedPassword,
                $profile,
                0, // two_factor desactivado
                'propietario' // rol
            );
            
            if ($result) {
                // 7. Registrar el movimiento de instalación
                require_once '../../php/movimientos/MovementController.php';
                $movementController = new MovementController();
                $movementController->addMovement(
                    $formusername,
                    "instaló el sistema y se convirtió en propietario",
                    date('Y-m-d H:i:s'),
                    "correcto"
                );
                
                // 8. Redirigir al login
                header('Location: ../../vistas/usuarios/login.php');
                exit();
            } else {
                throw new Exception("No se pudo crear el usuario propietario");
            }
            
        } catch (Exception $e) {
            error_log("Error en la instalación: " . $e->getMessage());
            $error = 'error_instalacion';
        }
    }
    
    // Si hubo error, redirigir con el error en la URL
    if ($error !== null) {
        header("Location: install.php?error=" . urlencode($error) . "&username=" . urlencode($formusername) . "&email=" . urlencode($formemail));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon" />
    <title>Instalación</title>
</head>

<body>
    <div class="install-card p-5">
        <div class="text-center mb-4">
            <i class="fas fa-database fa-4x text-primary mb-3"></i>
            <h2>Instalación del sistema</h2>
            <p class="text-muted">Configura tu Biblioteca Multimedia</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php
                switch ($_GET['error']) {
                    case 'campos_vacios':
                        echo 'Por favor, completa todos los campos.';
                        break;
                    case 'email_invalido':
                        echo 'El email no es válido.';
                        break;
                    case 'password_corta':
                        echo 'La contraseña debe tener al menos 8 caracteres.';
                        break;
                    case 'passwords_no_coinciden':
                        echo 'Las contraseñas no coinciden.';
                        break;
                    case 'error_instalacion':
                        echo 'Error durante la instalación. Revisa los logs.';
                        break;
                    default:
                        echo 'Error desconocido.';
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <!-- Campo del nombre de usuario -->
            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-user fa-lg"></i>
                </span>
                <div class="form-floating">
                    <input type="text" id="username" name="username" class="form-control"
                        placeholder="Nombre de usuario" required />
                    <label for="username">Nombre del usuario propietario</label>
                </div>
            </div>

            <!-- Campo del correo electrónico -->
            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-envelope fa-lg"></i>
                </span>
                <div class="form-floating">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Correo electrónico"
                        required />
                    <label for="email">Correo electrónico</label>
                </div>
            </div>

            <!-- Campo de la contraseña -->
            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-lock fa-lg"></i>
                </span>
                <div class="form-floating">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña"
                        required />
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
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                        placeholder="Repite la contraseña" required />
                    <label for="confirm_password">Repite la contraseña</label>
                </div>
                <button type="button" onclick="showRepeatPassword()" class="input-group-text">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-rocket me-2"></i>Instalar Sistema
            </button>
        </form>

        <div class="mt-4 text-center text-muted small">
            <i class="fas fa-info-circle me-1"></i>
            Se creará la base de datos y todas las tablas necesarias
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al archivo JS que permite mostrar la contraseña -->
    <script src="../../js/usuarios/show_password.js"></script>
</body>

</html>