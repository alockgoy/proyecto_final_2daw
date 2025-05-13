// Función para recargar después de 3 segundos
function redirect() {
    console.log("Recargando la página actual");
    setTimeout(function () {
        window.location.reload();
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