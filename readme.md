# 🧩 Cómo instalar el sistema

Hay algunas cosas que hay que hacer manualmente.

---

### ⚙️ Archivo de configuración

En la ruta:

```
[raíz-proyecto]/php
```

Existe un archivo llamado `config.empty.php`.  
Ese archivo hay que **renombrarlo** a `config.php` o crear una **copia** del archivo y llamarlo `config.php`.

Una vez hecho, se encuentra el siguiente trozo de código:

```php
$host = "";
$dbname = "";
$username = "";
$password = "";
```

- `$host` → Es la dirección de la base de datos. Por ejemplo, puede ser `"localhost"`.  
- `$dbname` → Es el nombre de la base de datos, **siempre** debe ser `"BibliotecaMultimedia"`.  
- `$username` → Es el nombre del usuario que se conecta a la base de datos.  
  Dependiendo del entorno, puede ser un usuario específico o el usuario administrador (a elección).  
- `$password` → Es la contraseña del usuario que se conecta a la base de datos.

---

### 📧 Funciones que envían correos electrónicos

Esta aplicación usa la librería **PHPMailer** para enviar correos electrónicos según el caso.  
Los archivos que hacen uso de esto son:

- `[raíz-proyecto]/vistas/usuarios/two_factor.php`  
- `[raíz-proyecto]/vistas/usuarios/recover_password.php`  
- `[raíz-proyecto]/php/reportar.php`

Estos archivos comparten el siguiente bloque de código:

```php
$mail->Username = 'correo'; // TU correo de Gmail
$mail->Password = 'clave'; // Contraseña de la aplicación generada
```

Dependiendo del proveedor, el proceso puede variar.  
En resumen, hay que **generar una clave de autenticación** que permita a la aplicación usar tu cuenta de correo electrónico para enviar mensajes.

---

### 🛠️ Resto de la configuración

Una vez configurados los apartados anteriores, se puede abrir la aplicación.  
Al no existir la base de datos (por ser la primera ejecución), se redirigirá a un formulario que pedirá:

- nombre de usuario  
- correo electrónico  
- contraseña  

Esto creará un usuario con rol **propietario**.  
Este usuario podrá hacer todo en la aplicación excepto:

- borrar su cuenta  
- cambiar su rol  
- conceder a otro usuario el rol propietario  

Si el proceso de instalación ha salido correctamente, la página redirigirá al **inicio de sesión**.  
A partir de ahí ya se debería poder usar con normalidad, y no se podrá volver a acceder al formulario de instalación.

---

📝 *(Si se me ha olvidado algún paso, actualizaré este archivo).*


[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/alockgoy/proyecto_final_2daw)
