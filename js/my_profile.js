// Función para recargar después de 3 segundos
function redirect() {
    console.log("Recargando la página actual");
    console.log("Redirigiendo con GET limpio");
    setTimeout(function () {
        window.location.href = window.location.pathname;
    }, 3000);
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log("Archivo js de mi perfil cargado correctamente");
    
    // Buscar mensajes de éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        console.log("Mensaje de éxito encontrado, preparando redirección");
        
        // Iniciar la redirección automática
        redirect();
    }
});