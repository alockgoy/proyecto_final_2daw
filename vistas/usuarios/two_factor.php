<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Comprobar que existe una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si no existe una sesión, redirigir
if (!isset($_SESSION['two_factor'])) {
    header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    exit();
}

// Traer los archivos necesarios
require_once '../../php/usuarios/UserController.php';
require_once '../../php/usuarios/User.php';

// Crear instancia del controlador
$controller = new UserController();

// Traer el archivo autoload del php mailer
require '../../vendor/autoload.php';

// Usar el php mailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Comprobar si el código ha caducado
if (isset($_SESSION['six_digit_code_expiration']) && time() > $_SESSION['six_digit_code_expiration']) {
    // Eliminar datos de la sesión relacionados con la verificación
    unset($_SESSION['two_factor']);
    unset($_SESSION['six_digit_code']);
    unset($_SESSION['six_digit_code_expiration']);

    // Mensaje de error
    $error = "El código ha caducado, por favor vuelve a iniciar sesión.";
} else {


    $error = "";

    // Generar un código aleatorio de 6 dígitos
    if (!isset($_SESSION['six_digit_code'])) {
        $_SESSION['six_digit_code'] = random_int(100000, 999999);

        // Temporizador de 5 minutos
        $_SESSION['six_digit_code_expiration'] = time() + (5 * 60);

        // Validar que el correo existe y es válido
        if (!isset($_SESSION['two_factor']) || !filter_var($_SESSION['two_factor'], FILTER_VALIDATE_EMAIL)) {
            $error = "Error: escribe un correo válido.";
            exit();
        }

        // Intentar enviar el correo con el código
        try {
            $email = $_SESSION['two_factor'];

            // Configurar SMTP
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Servidor SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'correo'; // TU correo de Gmail
            $mail->Password = 'clave'; // Contraseña de la aplicación generada
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurar el charset
            $mail->CharSet = 'UTF-8';

            // Configuración del correo
            $mail->setFrom('correo', 'usuario'); // De: el correo del usuario que genera la contraseña
            $mail->addAddress($email); // A: el correo de destino
            //$mail->addReplyTo($correoUsuario); // Opción de responder al correo del usuario

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = "Código verificación en 2 pasos.";
            $mail->Body = "
                    <p>
                        ¡Hola $email! Se ha recibido una solicitud para iniciar sesión en tu cuenta en Biblioteca multimedia
                        y tienes la verificación en 2 pasos activada. 
                    </p>
                    <p>
                        Tu código de inicio de sesión: <strong>{$_SESSION['six_digit_code']}</strong>
                    </p>
                    <p>
                        <strong>Ten en cuenta, </strong> dentro de 5 minutos este código caducará.
                    </p>";

            // Enviar el correo
            $mail->send();
        } catch (Exception $e) {
            $error = "Error al enviar el código de verificación: {$mail->ErrorInfo}";
        }
    }

    // Comprobar que se ha pulsado el botón de envío
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        // Obtener los valores introducidos en el formulario
        $input_code = implode('', array_map('trim', $_POST['2fa'] ?? []));

        // Comprobar que el código introducido coincide con el generado
        if ($input_code == $_SESSION['six_digit_code']) {

            // Obtener el email de la sesión
            $email = $_SESSION['two_factor'];

            // Eliminar la sesión de la verificación
            unset($_SESSION['two_factor']);
            unset($_SESSION['six_digit_code']);
            unset($_SESSION['six_digit_code_expiration']);

            // Rellenar los datos de la sesión
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $controller->getUsernameByEmail($email);

            header("Location: ../peliculas/movies.php");
            exit();
        } else {
            $error = "El código introducido es incorrecto. Por favor, inténtalo de nuevo.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación en 2 pasos</title>

    <!-- Enlace al css de bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" type="text/css" href="../../css/usuarios/two_factor.css">

    <!-- Para iconos -->
    <link rel="shortcut icon" href="../../img/iconos_navegador/usuario.png" type="image/x-icon" />
</head>

<body>
    <div class="container">
        <br>
        <div class="row">
            <div class="col-lg-5 col-md-7 mx-auto my-auto">
                <div class="card">
                    <div class="card-body px-lg-5 py-lg-5 text-center">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png"
                            class="rounded-circle avatar-lg img-thumbnail mb-4" alt="profile-image">
                        <h2 class="text-info">Verificación en 2 pasos</h2>
                        <p class="mb-4">Por favor, introduce el código de 6 dígitos que has recibido por correo:</p>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="row mb-4">
                                <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2">
                                    <input type="text" name="2fa[]" class="form-control text-lg text-center"
                                        placeholder="_" aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2">
                                    <input type="text" name="2fa[]" class="form-control text-lg text-center"
                                        placeholder="_" aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2">
                                    <input type="text" name="2fa[]" class="form-control text-lg text-center"
                                        placeholder="_" aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 pe-0 pe-md-2">
                                    <input type="text" name="2fa[]" class="form-control text-lg text-center"
                                        placeholder="_" aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 pe-0 pe-md-2">
                                    <input type="text" name="2fa[]" class="form-control text-lg text-center"
                                        placeholder="_" aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 pe-0 pe-md-2">
                                    <input type="text" name="2fa[]" class="form-control text-lg text-center"
                                        placeholder="_" aria-label="2fa">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn bg-info btn-lg my-4">Continuar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>