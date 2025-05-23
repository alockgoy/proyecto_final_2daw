<?php
//iniciar la sesión
session_start();

//obtener la IP actual del cliente
$ipActual = $_SERVER['REMOTE_ADDR'];

//comprobar si existe una IP almacenada en la sesión
if (!isset($_SESSION['ip_address'])) {
    //si no existe, se almacena la IP actual
    $_SESSION['ip_address'] = $ipActual;
} else {
    //comparar la IP actual con la IP almacenada en la sesión
    if ($_SESSION['ip_address'] !== $ipActual) {
        //si las IPs no coinciden, destruir la sesión y redirigir al usuario
        session_unset(); //limpiar variables de sesión
        session_destroy(); //destruir la sesión
        exit();
    }
}

// Opcional: imprimir un mensaje de seguridad para pruebas
// echo "<p>IP actual: $ipActual</p>";
?>
