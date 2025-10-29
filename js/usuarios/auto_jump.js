document.addEventListener("DOMContentLoaded", function () {
    console.log("Archivo de los saltos cargado");

    const inputs = document.querySelectorAll('input[name="2fa[]"]');

    inputs.forEach((input, index) => {
        input.addEventListener("input", function () {
            if (this.value.length === 1) {
                // Si hay un siguiente input, enfocar en él
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });

        input.addEventListener("keydown", function (event) {
            // Si se presiona la tecla de retroceso y el campo está vacío, volver al anterior
            if (event.key === "Backspace" && this.value === "") {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });
    });
});