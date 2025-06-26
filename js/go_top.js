window.onscroll = function () {

    // Al superar cierto nivel de scrolleo hacia abajo
    if (document.documentElement.scrollTop > 100) {

        // Obtener el contenedor del botón
        document.querySelector(".go-top-container")
            .classList.add("show"); // Hacer que se muestre

    } else {

        // Obtener el contenedor del botón
        document.querySelector(".go-top-container")
            .classList.remove("show"); // Hacer que se no muestre

    }
}

// Hacer que el contenedor reaccione a los clicks
document.querySelector(".go-top-container")
    .addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });