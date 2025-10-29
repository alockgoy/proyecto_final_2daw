function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var button = field.nextElementSibling;
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