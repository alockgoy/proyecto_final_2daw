<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/errores.css" />
    <title>Error</title>
</head>

<body>

    <?php
    //depuración
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    //traer el archivo autoload del phpmailer
    require '../vendor/autoload.php';

    //usar el php mailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    //comprobar que se ha pulsado el botón de envío del formulario de reportar un error
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        //asignar los valores introducidos a variables
        $tipoError = $_POST['errorType'];
        $correoUsuario = $_POST['userEmail'];
        $errorDetallado = $_POST['errorDetails'];

        /* Comprobaciones*/

        //comprobar que no hay campos vacíos
        if (empty($tipoError) || empty($correoUsuario) || empty($errorDetallado)) {
            echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
            die("<p class='error'>No puede haber campos vacíos</p>");
        }//comprobar que el correo cumple con la norma 
        elseif (!filter_var($correoUsuario, FILTER_VALIDATE_EMAIL)) {
            echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
            die("<p class='error'>El correo introducido no es válido</p>");
        }//comprobar que la opción seleccionada es válida
        elseif ($tipoError != "other" && $tipoError != "website" && $tipoError != "account") {
            echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
            die("<p class='error'>La opción introducida no es correcta.</p>");
        }

        /* Fin de comprobaciones (por ahora) */

        //configurar PHPMailer
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
            $mail->setFrom($correoUsuario, 'Usuario'); // De: el correo del usuario que reporta
            $mail->addAddress('correo'); // A: el correo de destino
            $mail->addReplyTo($correoUsuario); // Opción de responder al correo del usuario
    
            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = "Nuevo reporte: $tipoError";
            $mail->Body = "<p><strong>Correo del usuario:</strong> $correoUsuario</p>
                      <p><strong>Detalles del error:</strong> $errorDetallado</p>";

            // Enviar el correo
            $mail->send();
            header("Location: ../index.html");
        } catch (Exception $e) {
            echo "<p class='error'>Error al enviar el correo: {$mail->ErrorInfo}</p>";
        }


    }
    ?>

</body>

</html>