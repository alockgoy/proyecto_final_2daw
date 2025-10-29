document.addEventListener("DOMContentLoaded", function () {
    console.log("Buscador de usuarios inicializado");

    // Lanzar la escucha cada vez que se pulse una tecla
    document.addEventListener("keyup", function (e) {
        if (e.target.matches("#buscador_usuarios")) {

            console.log("Buscando:", e.target.value);

            // Limpiar la búsqueda con Escape
            if (e.key === "Escape") e.target.value = "";

            const textoBusqueda = e.target.value.toLowerCase().trim();

            // Seleccionar todas las filas de usuarios (excepto el header)
            document.querySelectorAll("tbody tr").forEach(fila => {

                // Obtener todos los datos de la fila para buscar en múltiples campos
                const alias = fila.querySelectorAll("td")[0].textContent.toLowerCase(); // Alias/Username
                const correo = fila.querySelectorAll("td")[1].textContent.toLowerCase(); // Email
                const rol = fila.querySelectorAll("td")[4].textContent.toLowerCase(); // Rol

                // Buscar en cualquiera de los campos
                const coincide =
                    alias.includes(textoBusqueda) ||
                    correo.includes(textoBusqueda) ||
                    rol.includes(textoBusqueda);

                // Mostrar u ocultar la fila según coincidencia
                if (coincide) {
                    fila.style.display = ""; // Mostrar
                } else {
                    fila.style.display = "none"; // Ocultar
                }
            });
        }
    });
});