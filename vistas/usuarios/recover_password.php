<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Traer los archivos necesarios
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Traer el archivo autoload del php mailer
require '../../vendor/autoload.php';

// Usar el php mailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Crear instancia del controlador
$userController = new UserController();

// Obtener el correo introducido en el formulario
if (isset($_POST['email'])) {

    // Validar que el correo cumple con el formato
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

        // Comprobar que el correo existe
        if ($userController->checkEmailExists($_POST['email'])) {

            // Intentar actualizar la contraseña
            try {
                // Asignar el correo a una variable (para posteriormente mandar el correo)
                $email = $_POST['email'];

                // Generar una nueva contraseña aleatoria
                $newPassword = bin2hex(random_bytes(8));

                // Llamar al método para actualizar la contraseña
                $userController->resetUserPassword($_POST['email'], $newPassword);

                // Enviar el correo con la contraseña actualizada
                $mail = new PHPMailer(true);
                try {
                    // Configurar SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Servidor SMTP de Gmail
                    $mail->SMTPAuth = true;
                    $mail->Username = 'correo'; // TU correo de Gmail
                    $mail->Password = 'clave'; // Contraseña de la aplicación generada
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Configuración del correo
                    $mail->setFrom('correo', 'usuario'); // De: el correo del usuario que genera la contraseña
                    $mail->addAddress($email); // A: el correo de destino
                    //$mail->addReplyTo($correoUsuario); // Opción de responder al correo del usuario

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = "Clave nueva generada.";
                    $mail->Body = "
                    <p>
                        ¡Hola $email! Se ha recibido una solicitud para cambiar tu contraseña en la página de biblioteca multimedia. 
                        ¡No te preocupes!
                    </p>
                    <p>
                        Tu nueva clave es: <strong>$newPassword</strong>
                    </p>
                    <p>
                        Puedes cambiarla siempre que quieras desde el apartado de modificar tu cuenta
                    </p>";

                    // Enviar el correo
                    $mail->send();
                    header("Location: ../index.html");
                } catch (Exception $e) {
                    $error = "Error al enviar la nueva contraseña: {$mail->ErrorInfo}";
                }

                $success = "Contraseña actualizada. Por favor, comprueba tu correo.";
            } catch (\Throwable $th) {
                $error = "Error al reestablecer tu contraseña: " + $th;
            }

        } else {
            $error = "No se encuentra el correo ";
        }
    } else {
        $error = "El correo introducido no es válido";
    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>

    <!-- Enlace al CSS de bootstrap -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Para iconos -->
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon" />
</head>

<body>
    <!-- Password Reset 8 - Bootstrap Brain Component -->
    <section class="bg-light p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-11">
                    <div class="card border-light-subtle shadow-sm">
                        <div class="row g-0">
                            <div class="col-12 col-md-6">
                                <img class="img-fluid rounded-start w-100 h-100 object-fit-cover" loading="lazy"
                                    src="https://imgs.search.brave.com/VfhZWPqMygKEFDnd-YAt4L9tfo2Pss3aqakuKamp1yc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzA0LzgxLzMzLzY3/LzM2MF9GXzQ4MTMz/Njc5M18yUEF5czcx/N2cxdjBhbXl1aTJ3/WEJzNkc1UTNmcGph/cS5qcGc"
                                    alt="Welcome back you've been missed!">
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
                                <div class="col-12 col-lg-11 col-xl-10">
                                    <div class="card-body p-3 p-md-4 p-xl-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-5">
                                                    <div class="text-center mb-4">
                                                        <a href="#!">
                                                            <img src="https://bootstrapbrain.com/demo/components/password-resets/password-reset-8/assets/img/bsb-logo.svg"
                                                                alt="BootstrapBrain Logo" width="175" height="57">
                                                        </a>
                                                    </div>

                                                    <!-- Mostrar errores -->
                                                    <?php if (!empty($error)): ?>
                                                        <div class="alert alert-danger" role="alert">
                                                            <i
                                                                class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Mostrar mensaje de aprobación -->
                                                    <?php if (!empty($success)): ?>
                                                        <div class="alert alert-success" role="alert"
                                                            data-redirect="./movies.php">
                                                            <i
                                                                class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <h2 class="h4 text-center">Recuperar contraseña</h2>
                                                    <h3 class="fs-6 fw-normal text-secondary text-center m-0">
                                                        Por favor, escribe el correo electrónico de tu cuenta.
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <form method="POST">
                                            <div class="row gy-3 overflow-hidden">
                                                <div class="col-12">
                                                    <div class="form-floating mb-3">
                                                        <input type="email" class="form-control" name="email" id="email"
                                                            placeholder="name@example.com" required>
                                                        <label for="email" class="form-label">Email</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button class="btn btn-dark btn-lg" type="submit">
                                                            Recuperar contraseña
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-center mt-5">
                                                    <a href="./login.php"
                                                        class="link-secondary text-decoration-none">Iniciar sesión</a>
                                                    <a href="./singup.php"
                                                        class="link-secondary text-decoration-none">Registrarse</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>