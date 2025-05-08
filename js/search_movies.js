document.addEventListener("DOMContentLoaded", function() {
    console.log("Buscador inicializado");
    
    // Lanzar la escucha cada vez que se pulse una tecla
    document.addEventListener("keyup", function(e) {
        if (e.target.matches("#buscador")) {

            console.log("Buscando:", e.target.value);
            
            // Limpiar la búsqueda con Escape
            if (e.key === "Escape") e.target.value = "";
            
            const textoBusqueda = e.target.value.toLowerCase().trim();
            
            // Seleccionar todas las películas
            document.querySelectorAll(".pelicula").forEach(pelicula => {

                // Obtener el título de la película
                const titulo = pelicula.querySelector(".card-title").textContent.toLowerCase();

                // Ocultar/mostrar la columna (contenedor padre) en lugar de solo la tarjeta
                const columna = pelicula.closest(".col");
                
                // Mostrar u ocultar según coincidencia
                if (titulo.includes(textoBusqueda)) {
                    columna.style.display = ""; // Mostrar (valor por defecto)
                } else {
                    columna.style.display = "none"; // Ocultar
                }
            });
        }
    });
});