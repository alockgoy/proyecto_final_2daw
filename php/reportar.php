<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/usuarios/errores.css" />
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
        elseif (!in_array($tipoError, ['account', 'website', 'other'])) {
            echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
            die("<p class='error'>La opción introducida no es correcta.</p>");
        }

        // Comprobación del archivo adjunto (opcional)
        $archivoAdjunto = null;
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = $_FILES['screenshot']['name'];
            $tipoArchivo = mime_content_type($_FILES['screenshot']['tmp_name']);
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            $tamanioArchivo = $_FILES['screenshot']['size'];

            // Comprobar la extensión del archivo
            if (!in_array($extension, $extensionesPermitidas)) {
                echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
                die("<p class='error'>Formato de archivo no permitido. Solo se aceptan JPG, JPEG, PNG o WEBP.</p>");
            }

            // Comprobar el tamaño del archivo (máximo 3 MB)
            if ($tamanioArchivo > 3 * 1024 * 1024) { // 3 MB en bytes
                echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
                die("<p class='error'>El archivo adjunto no puede pesar más de 3 MB.</p>");
            }

            $archivoAdjunto = $_FILES['screenshot']['tmp_name'];
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

            // Adjuntar archivo si es válido
            if ($archivoAdjunto) {
                $mail->addAttachment($archivoAdjunto, $nombreArchivo);
            }

            // Enviar el correo
            $mail->send();
            header("Location: ../index.html");
        } catch (Exception $e) {
            echo "<a href='../index.html'>Volver atrás</a><br/><br/>";
            echo "<p class='error'>Error al enviar el correo: {$mail->ErrorInfo}</p>";
        }


    }
    ?>

</body>

</html>