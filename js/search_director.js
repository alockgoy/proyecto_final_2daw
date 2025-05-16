document.addEventListener("DOMContentLoaded", function() {
    console.log("Buscador de directores inicializado");
    
    // Lanzar la escucha cada vez que se pulse una tecla
    document.addEventListener("keyup", function(e) {
        if (e.target.matches("#buscador_directores")) {

            console.log("Buscando director:", e.target.value);
            
            // Limpiar la búsqueda con Escape
            if (e.key === "Escape") e.target.value = "";
            
            const textoBusqueda = e.target.value.toLowerCase().trim();
            
            // Seleccionar todas las películas
            document.querySelectorAll(".pelicula").forEach(pelicula => {

                // Obtener el director de la película
                const director = pelicula.querySelector(".director").textContent.toLowerCase();

                // Ocultar/mostrar la columna (contenedor padre) en lugar de solo la tarjeta
                const columna = pelicula.closest(".col");
                
                // Mostrar u ocultar según coincidencia
                if (director.includes(textoBusqueda)) {
                    columna.style.display = ""; // Mostrar (valor por defecto)
                } else {
                    columna.style.display = "none"; // Ocultar
                }
            });
        }
    });
});