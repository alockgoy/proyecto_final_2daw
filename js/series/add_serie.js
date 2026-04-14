// Función para redirigir después de 3 segundos
function redirect(url) {
    console.log("Redirigiendo a:", url);
    setTimeout(function () {
        window.location.href = url;
    }, 3000);
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log("Archivo js de añadir series cargado correctamente");
    
    // Buscar mensajes de éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        console.log("Mensaje de éxito encontrado, preparando redirección");
        
        // Obtener la URL de redirección del atributo data-redirect
        const redirectUrl = successAlert.getAttribute('data-redirect') || './series.php';
        
        // Iniciar la redirección automática
        redirect(redirectUrl);
    }
});

// Validación de longitud de sinopsis antes de enviar el formulario
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const synopsis = document.getElementById('synopsis');
    const MAX_LENGTH = 600;

    // Crear el div de error una sola vez y añadirlo tras el textarea
    const errorDiv = document.createElement('div');
    errorDiv.id = 'synopsis-error';
    errorDiv.className = 'invalid-feedback';
    errorDiv.style.display = 'none';
    errorDiv.textContent = `La sinopsis no puede superar los ${MAX_LENGTH} caracteres.`;
    synopsis.parentElement.appendChild(errorDiv);

    // Marcar error en tiempo real mientras se escribe
    synopsis.addEventListener('input', function () {
        if (synopsis.value.length > MAX_LENGTH) {
            synopsis.classList.add('is-invalid');
            errorDiv.style.display = 'block';
        } else {
            synopsis.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
        }
    });

    // Bloquear el envío si la sinopsis es demasiado larga
    form.addEventListener('submit', function (e) {
        if (synopsis.value.length > MAX_LENGTH) {
            e.preventDefault();
            synopsis.classList.add('is-invalid');
            errorDiv.style.display = 'block';
            synopsis.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
