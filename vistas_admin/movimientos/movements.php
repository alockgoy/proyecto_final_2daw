<?php

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
require_once '../../php/movimientos/MovementController.php';
require_once '../../php/movimientos/Movement.php';

// Crear instancia de los controladores
$userController = new UserController();
$movementController = new MovementController();

// Obtener datos del usuario actual
$username = $_SESSION['username'];
$userRol = $userController->getUserRol($username);

// Obtener la foto de perfil del usuario
$profilePicture = $userController->getUserProfilePicture($username);

// Comprobar que el usuario sea 'root'
if ($userRol != "root") {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

// Obtener todos los movimientos
$movimientos = $movementController->index();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos</title>

    <!--Enlace al CSS de bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../css/go_top.css" type="text/css" rel="stylesheet" />

    <!--Para iconos-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid px-2">
                <!-- Foto de perfil del usuario -->
                <a class="navbar-brand" href="">
                    <img src="<?php echo !empty($profilePicture) ? '../../' . htmlspecialchars($profilePicture) : '../../img/avatares_usuarios/default.jpg'; ?>"
                        width="50" height="50" class="rounded-circle" alt="Foto de perfil">
                </a>

                <!--Botón para colapsar la barra en pantallas pequeñas-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--Elementos de la barra de navegación-->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <p class="nav-link" href=""></p>
                        </li>
                    </ul>

                    <span class="navbar-text text-light">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container-fluid px-4 mb-5 mt-2">
            <div class="table-responsive">
                <table class="table table-striped table-dark text-center align-middle">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Ha:</th>
                            <th scope="col">¿Cuándo?</th>
                            <th scope="col">Resultado</th>
                        </tr>
                    </thead>

                    <!-- Mostrar todos los movimientos -->
                    <tbody>
                        <?php foreach ($movimientos as $movimiento): ?>
                            <tr class="tr">
                                <th>
                                    <?php echo htmlspecialchars($movimiento['id_movement']); ?>
                                </th>

                                <td>
                                    <?php echo htmlspecialchars($movimiento['username']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($movimiento['he_did']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($movimiento['moment']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($movimiento['with_result']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Botón para volver arriba -->
    <div class="go-top-container">
        <div class="go-top-button">
            <i class="fas fa-chevron-up"></i>
        </div>
    </div>

    <footer class="mt-5 fixed-bottom">
        <!--Barra de navegación-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid px-2">
                <!--Botón para colapsar la barra en pantallas pequeñas-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFooter">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--Elementos de la barra de navegación-->
                <div class="collapse navbar-collapse" id="navbarFooter">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../usuarios/users.php">Volver atrás</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../../vistas/usuarios/logout.php">
                                <i class="fa-solid fa-right-from-bracket me-1"></i>
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </footer>

    <!-- Enlace al archivo JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enlace al archivo JavaScript de confirmar eliminación -->
    <script src="../../js/confirm_modal.js"></script>
</body>

</html>