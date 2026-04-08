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

Una vez hecho, rellena todas las credenciales en ese único archivo:

```php
// ── Base de datos ──────────────────────────────────────────────
$host     = "";       // Ej: "localhost"
$dbname   = "";       // Siempre debe ser "BibliotecaMultimedia"
$username = "";       // Usuario de la base de datos
$password = "";       // Contraseña de la base de datos

// ── Correo (PHPMailer) ─────────────────────────────────────────
define('MAIL_USERNAME', '');   // Tu correo de Gmail
define('MAIL_PASSWORD', '');   // Contraseña de aplicación generada

// ── The Movie Database API ─────────────────────────────────────
define('TMDB_API_KEY', '');    // Tu clave API de themoviedb.org
```

> ⚠️ `config.php` está en `.gitignore`. **Nunca** lo subas al repositorio.

---

### 📧 Correo electrónico (PHPMailer)

Esta aplicación usa **PHPMailer** para enviar correos en los siguientes casos:

- Autenticación de dos factores
- Recuperación de contraseña
- Formulario de reporte de errores

Las credenciales se configuran **únicamente** en `config.php` (ver arriba). No hay que tocar ningún otro archivo.

Para obtener una contraseña de aplicación de Gmail:
1. Activa la verificación en dos pasos en tu cuenta de Google.
2. Ve a **Gestionar tu cuenta de Google → Seguridad → Contraseñas de aplicaciones**.
3. Genera una contraseña para "Correo" y cópiala en `MAIL_PASSWORD`.

---

### 🎬 API de The Movie Database (TMDB)

Al añadir películas o series, el formulario permite buscar el título y autorellenar campos como nombre, sinopsis, géneros, año, puntuación y póster.

La clave API se configura **únicamente** en `config.php` (ver arriba). No hay que tocar ningún otro archivo.

**¿Cómo obtener la clave?**
1. Entra en: https://www.themoviedb.org/settings/api
2. Créate una cuenta.
3. En tu perfil, busca la sección **API**.
4. Selecciona el plan **gratuito** para uso no comercial.
5. Rellena el formulario. La clave aparecerá como **"Clave de la API"**.

> La API tiene un límite de **40 peticiones por segundo**. Más info: https://developer.themoviedb.org/docs/rate-limiting

---

#### ❓ Preguntas frecuentes

**¿Por qué tengo que renombrar los archivos?**  
Porque los enlaces que apuntan a ellos tienen esos nombres. El código subido a GitHub **NO** dispone de mi clave API por motivos evidentes.

**¿Por qué en el caso de series el nombre del archivo es distinto?**  
Porque cuando empecé a implementar el uso de API, lo hice con la plataforma "Open Movie Database", pero autorellenaba los campos en inglés y yo quería que se rellenen en español. Por eso cambié a "The Movie Database", pero a los archivos de películas no les cambié los nombres.

**¿Esa API que mencionas es gratuita?**  
Sí, pero tiene un uso limitado a **40 peticiones por segundo**.  
Más información: https://developer.themoviedb.org/docs/rate-limiting

**¿Por qué no se autorrellenan todos los campos?**  
No me extenderé mucho porque el propósito de esta aplicación se puede leer en `[raíz-proyecto]/html/manual.html`.  

En España no es ilegal hacer una copia de seguridad **personal** de un DVD comprado legalmente. Dicha copia, dependiendo del formato y calidad con que se haga, tendrá diferentes parámetros según el usuario que <u>no</u> se pueden autorellenar con el uso de la API.

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
