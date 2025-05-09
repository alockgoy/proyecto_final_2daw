// Función para redirigir después de 3 segundos
function redirect(url) {
    console.log("Redirigiendo a:", url);
    setTimeout(function () {
        window.location.href = url;
    }, 3000);
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log("Archivo js cargado correctamente");
    
    // Buscar mensajes de éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        console.log("Mensaje de éxito encontrado, preparando redirección");
        
        // Obtener la URL de redirección del atributo data-redirect
        const redirectUrl = successAlert.getAttribute('data-redirect') || './movies.php';
        
        // Iniciar la redirección automática
        redirect(redirectUrl);
    }
});