# Proyecto final 
Este va a ser un archivo readme principalmente para dejar clara la organización y funcionalidades disponibles que tendrá este proyecto.

Importante el hecho de que no puedo superar las 40 horas de duración en el desarrollo.

El proyecto consistirá en una biblioteca de películas y series, en la que el usuario podrá añadir las películas y series que ha visto / desea ver.

También planeo que exista (ya sea a la entrega del proyecto o a posterior) un 'super-usuario' (alias root / admin) que pueda hacer todo, con la idea de poder ayudar a un usuario si tiene un problema que no sabe resolver.

La página web será desplegada en mi NAS y contará con enlaces al repositorio de Github para poder visualizar el código.

Un usuario no registrado (o registrado) podrá reportar errores de la aplicación.

## ¿Qué puede hacer un usuario?
- Puede crearse una cuenta.
    - (el id se generará de forma automática en la base de datos)
    - nombre de usuario*
    - foto de perfil
    - correo electrónico*
    - contraseña*
    - (el salt se generará de forma automática en la base de datos)
    - ¿desea autenticación en 2 pasos? (s/n)

- Puede iniciar sesión con su cuenta.
    - nombre de usuario*
    - contraseña
    - código de la verificación en 2 pasos (si la tiene activada)

- Puede solicitar la eliminación de su cuenta.

- Puede solicitar la recuperación de su contraseña si se le ha olvidado (se le enviará una nueva por correo electrónico).

- Puede modificar sus datos
    - nombre de usuario
    - foto de perfil
    - correo electrónico
    - contraseña
    - verificación en 2 pasos

- Puede añadir películas:
    - Foto de portada*
    - Nombre*
    - Sinopsis
    - Director*
    - Género* (acción/aventura, animación, anime, ciencia ficción, cortometraje, comedia, deportes, documental, drama, familiar, fantasía, guerra, terror, musical, suspense, romance, vaqueros, misterio)
    - Idioma(s)*
    - Año*
    - Calidad* (4K, 1440p, 1080p, 720p, 420p, otro)
    - Valoración (de 1 a 10)

- Puede eliminar películas.
- Puede modificar datos de películas.

- Puede añadir series:
    - Foto de portada*
    - Nombre*
    - Género (los mismos que en películas)*
    - Idiomas*
    - Nº de temporadas*
    - ¿Está finalizada? (s/n)*
    - Año*
    - Valoración (de 1 a 10)

- Puede eliminar series.
- Puede modificar las series existentes.

- Puede buscar películas por nombre.
- Puede buscar películas por el nombre de su director.
- Puede buscar series por nombre.
- Puede buscar series completas.

### Funciones que estarán de base en el proyecto final
- Creación de una cuenta de usuario
- Modificación de datos de una cuenta de usuario
- Eliminación de una cuenta de usuario
- Posibilidad de contar con la verificación en 2 pasos (se manda un código por correo electrónico)
- Añadir películas a la biblioteca del usuario
- Modificar las películas existentes en la biblioteca del usuario
- Eliminar películas existentes en la biblioteca del usuario
- Las mismas operaciones con películas pero con las series
- Posibilidad de que un usuario reporte que ha olvidado su clave de acceso y recibir una nueva (se le genera automáticamente si existe el usuario y se le manda por correo electrónico)

### Funciones que me gustaría añadir más adelante
- Archivo log (se tratará de un csv que guardará todos los movimientos realizados en la página, con la idea de realizar depuraciones).
- Usuario administrador que tendrá privilegios absolutos sobre la aplicación.
- Conectarse a una base de datos tipo 'Filmaffinity' o derivadas para obtener los datos de las películas.
- El usuario puede solicitar la descarga de su biblioteca en formato CSV
- Navegar por películas que tienen secuelas

### Estructura del proyecto
```
```
├── prototipo/
├── sql/
├── css/
│   └── usuarios/
├── img/
│   ├── portadas_peliculas/
│   ├── portadas_series/
│   ├── iconos_navegador/
│   ├── otras/
│   └── avatares_usuarios/
├── vistas/
│   ├── peliculas/
│   ├── series/
│   └── usuarios/
├── js/
├── php/
│   ├── usuarios/
│   ├── peliculas/
│   └── series/
├── html/
└── vendor/
    ├── composer/
    └── phpmailer/
        └── phpmailer/
            ├── language/
            └── src/
```
```

### Atribuciones por iconos
- <a href="https://www.flaticon.es/iconos-gratis/perfiles-de-usuario" title="perfiles de usuario iconos">Perfiles de usuario iconos creados por yaicon - Flaticon</a>

- <a href="https://www.flaticon.es/iconos-gratis/film-fotografico" title="film fotográfico iconos">Film fotográfico iconos creados por Iconic Panda - Flaticon</a>

- <a href="https://www.flaticon.es/iconos-gratis/serie" title="serie iconos">Serie iconos creados por shmai - Flaticon</a>

- <a href="https://www.flaticon.es/iconos-gratis/usuario-seguro" title="usuario seguro iconos">Usuario seguro iconos creados por Muhammad Atif - Flaticon</a>

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/alockgoy/proyecto_final_2daw)