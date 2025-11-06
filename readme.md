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
### 🎬 Funciones que requieren el uso de claves API

Recientemente se ha implementado, tanto para añadir **películas** como **series**, la posibilidad de usar una clave API de [The Movie Database](https://api.themoviedb.org/3).

Esta opción permite buscar una película en el propio formulario y rellenar de forma automática los campos:

- Nombre  
- Dirección  
- Sinopsis  
- Géneros  
- Año  
- Puntuación  
- Póster  

#### 📽️ Configuración para películas

Si estás desplegando esta aplicación de manera local, verás un archivo llamado `omdb_search_clean.js` con el siguiente código:

```javascript
const TMDB_API_KEY = 'apikey'; // Cambiar este campo por tu clave api
```

Dicho contenido de la variable deberá ser sustituido por **tu clave API** de la plataforma "The Movie Database", y el archivo deberá ser **renombrado** a `omdb_search.js` o bien hacer una **copia** con este nombre.

#### 📺 Configuración para series

Encontrarás un archivo llamado `tmdb_search_clean.js` con el siguiente contenido:

```javascript
const TMDB_API_KEY = 'apikey'; // Cambiar este campo por tu clave api
```

Dicho contenido de la variable deberá ser sustituido por **tu clave API** de la plataforma "The Movie Database", y el archivo deberá ser **renombrado** a `tmdb_search.js` o bien hacer una **copia** con este nombre.

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

**¿Cómo solicito una API si despliego esta app de manera local?**  
1. Entra en este enlace: https://www.themoviedb.org/settings/api  
2. Créate una cuenta.  
3. En tu perfil, busca la sección de **API**.  
4. Selecciona el plan **gratuito** para uso <u>no comercial</u>.  
5. Rellena un formulario con algunos datos personales y de la aplicación.  
6. Una vez hecho todo lo anterior, al volver al enlace del paso 1, deberías ver algo llamado **"Clave de la API"**.

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
