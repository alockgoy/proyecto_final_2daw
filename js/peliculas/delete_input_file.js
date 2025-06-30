// Variables del formulario
var control = document.getElementById("poster");
var clearBn = document.getElementById("clear");

// Limpiar el input
clearBn.addEventListener("click", function(){
    control.value = '';
});
