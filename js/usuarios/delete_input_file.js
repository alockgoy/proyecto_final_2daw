// Variables del formulario
var control = document.getElementById("profile");
var clearBn = document.getElementById("clear");

// Limpiar el input
clearBn.addEventListener("click", function(){
    control.value = '';
});
