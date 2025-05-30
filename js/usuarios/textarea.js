//variable de reserva
let cambiado = false;

//posicionar el cursor
if (!cambiado) {
    const errorDetails = document.getElementById("errorDetails");
    const setCursorPosition = function () {
        this.setSelectionRange(0, 0);
        errorDetails.removeEventListener("click", setCursorPosition);
    };
    errorDetails.addEventListener("click", setCursorPosition);
    cambiado = true;
}