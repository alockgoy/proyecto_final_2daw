// Función para mostrar/ocultar contraseña principal
function showPassword() {
  var field = document.getElementById("password");
  var inputGroup = field.closest('.input-group');
  var button = inputGroup.querySelector('button[onclick="showPassword()"]');
  var icon = button.querySelector('i');

  if (field.type === "password") {
    field.type = "text";
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    field.type = "password";
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

// Función para mostrar/ocultar contraseña de confirmación
function showRepeatPassword() {
  var field = document.getElementById("confirm_password");
  var inputGroup = field.closest('.input-group');
  var button = inputGroup.querySelector('button[onclick="showRepeatPassword()"]');
  var icon = button.querySelector('i');

  if (field.type === "password") {
    field.type = "text";
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    field.type = "password";
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}