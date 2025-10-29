// Función que muestra solo las series completas
function showCompleteSeries() {
    console.log("Función de buscar las series completas lanzada");

    // Leer el estado checked
    const onlyComplete = document.getElementById("completeSeries").checked;

    document.querySelectorAll(".serie").forEach(serie => {

        const columna = serie.closest(".col");

        // Texto “si”/“no” en minúsculas
        const isComplete = serie.querySelector(".complete").textContent.trim().toLowerCase() === "si";

        if (onlyComplete) {
            // Ocultar las series no completas
            columna.style.display = isComplete ? "" : "none";
        } else {
            // Si NO está marcado, mostrar todas las series
            columna.style.display = "";
        }
    });
}


