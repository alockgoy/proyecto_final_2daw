// Función para recargar después de 3 segundos manteniendo el username
function redirect() {
    console.log("Recargando la página actual");
    console.log("Redirigiendo con GET limpio");
    setTimeout(function () {
        // Obtener el elemento de éxito para verificar si hay data-redirect personalizado
        const successAlert = document.querySelector('.alert-success');
        const dataRedirect = successAlert ? successAlert.getAttribute('data-redirect') : null;

        if (dataRedirect) {
            // Si hay un data-redirect específico, usarlo
            window.location.href = dataRedirect;
        } else {
            // Fallback: obtener el parámetro username de la URL actual
            const urlParams = new URLSearchParams(window.location.search);
            const username = urlParams.get('username');

            if (username) {
                window.location.href = `show_user.php?username=${encodeURIComponent(username)}`;
            } else {
                window.location.href = 'show_user.php';
            }
        }
    }, 3000);
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    console.log("Archivo js de show_user cargado correctamente");

    // Buscar mensajes de éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        console.log("Mensaje de éxito encontrado, preparando redirección");

        // Verificar si hay un campo de nuevo nombre de usuario para usar ese valor
        const newUsernameField = document.getElementById('new_username');
        if (newUsernameField && newUsernameField.value) {
            // Si hay un nuevo nombre de usuario, actualizar el data-redirect
            const newUsername = newUsernameField.value.trim();
            successAlert.setAttribute('data-redirect', `show_user.php?username=${encodeURIComponent(newUsername)}`);
        }

        // Iniciar la redirección automática
        redirect();
    }
});