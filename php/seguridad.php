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

// ============ PROTECCIÓN CSRF ============

/**
 * Generar token CSRF y almacenarlo en la sesión
 */
function generarTokenCSRF()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validar token CSRF del formulario
 */
function validarTokenCSRF($token)
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerar token CSRF (llamar después de validar)
 */
function regenerarTokenCSRF()
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Obtener input hidden con token CSRF para formularios
 */
function campoTokenCSRF()
{
    $token = generarTokenCSRF();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// Opcional: imprimir un mensaje de seguridad para pruebas
// echo "<p>IP actual: $ipActual</p>";
?>