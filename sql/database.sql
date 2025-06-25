/* Creación de la base de datos */
CREATE DATABASE IF NOT EXISTS BibliotecaMultimedia;
USE BibliotecaMultimedia;

/* Tabla usuarios */
CREATE TABLE Users(
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    salt VARCHAR(255) NOT NULL,
    password VARCHAR(300) NOT NULL,
    profile VARCHAR(255),
    two_factor BOOLEAN DEFAULT FALSE,
    rol VARCHAR(10) DEFAULT 'normal' CHECK (rol IN ('root', 'normal', 'solicita'))
);

/* Creación de la tabla películas */
CREATE TABLE Movies(
    id_movie INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(300) NOT NULL,
    synopsis VARCHAR(600),
    poster VARCHAR(255) NOT NULL,
    director VARCHAR(100) NOT NULL,
    gender VARCHAR(100) NOT NULL CHECK (gender IN (
        'acción/aventura', 'animación', 'anime', 
        'ciencia ficción', 'cortometraje', 'comedia', 'deportes', 
        'documental', 'drama', 'familiar', 'fantasía', 'guerra', 
        'terror', 'musical', 'suspense', 'romance', 'vaqueros', 'misterio'
    )),
    languages VARCHAR(100) NOT NULL,
    size FLOAT NOT NULL,
    year INT NOT NULL,
    quality VARCHAR(50) NOT NULL CHECK (quality IN (
        '4K', '1440p', '1080p', '720p', '420p', 'otro'
    )),
    backup VARCHAR(200),
    server VARCHAR(2) NOT NULL CHECK (server IN ('si', 'no')),
    rating FLOAT
);


/* Creación de la tabla series */
CREATE TABLE Series(
    id_serie INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(300) NOT NULL,
    poster VARCHAR(255) NOT NULL,
    gender VARCHAR(100) NOT NULL CHECK (gender IN (
        'acción/aventura', 'animación', 'anime', 
        'ciencia ficción', 'cortometraje', 'comedia', 'deportes', 
        'documental', 'drama', 'familiar', 'fantasía', 'guerra', 
        'terror', 'musical', 'suspense', 'romance', 'vaqueros', 'misterio'
    )),
    languages VARCHAR(100) NOT NULL,
    seasons INT NOT NULL,
    complete VARCHAR(2) NOT NULL CHECK (complete IN ('si', 'no')),
    year INT NOT NULL,
    quality VARCHAR(50) NOT NULL CHECK (quality IN (
        '4K', '1440p', '1080p', '720p', '420p', 'otro'
    )),
    backup VARCHAR(200),
    size FLOAT NOT NULL,
    server VARCHAR(2) NOT NULL CHECK (server IN ('si', 'no')),
    rating FLOAT
);


/* Tabla que sale de la unión de usuarios y películas */
CREATE TABLE Users_Movies(
    id_user INT,
    id_movie INT,
    PRIMARY KEY (id_user, id_movie),
    FOREIGN KEY (id_user) REFERENCES Users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_movie) REFERENCES Movies(id_movie) ON DELETE CASCADE
);

/* Tabla que sale de la unión de usuarios y series */
CREATE TABLE Users_Series(
    id_user INT,
    id_serie INT,
    PRIMARY KEY (id_user, id_serie),
    FOREIGN KEY (id_user) REFERENCES Users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_serie) REFERENCES Series(id_serie) ON DELETE CASCADE
);

/* Crear un usuario específico para esta base de datos y asignarle todos los permisos */
CREATE USER 'usuario_multimedia'@'localhost' IDENTIFIED BY 'Esta contraseña será cambiada';
GRANT ALL PRIVILEGES ON BibliotecaMultimedia.* TO 'usuario_multimedia'@'localhost';
FLUSH PRIVILEGES;

---------------------------------------------------------------------------------------------

/* Eliminar al usuario (por el motivo que sea) */
DROP USER 'usuario_multimedia'@localhost;

/* Consulta para obtener todos los usuarios */
SELECT * FROM Users;

/* Consulta para obtener las películas (sin filtrar por usuario) */
SELECT * FROM Movies;

/* Consulta para obtener las series (sin filtrar por usuario) */
SELECT * FROM Series;

/* Consulta para obtener los valores de la tabla de las películas vinculadas a un usuario */
SELECT * FROM Users_Movies;

/* Consulta para obtener los valores de la tabla series vinculadas a un usuario */
SELECT * FROM Users_Series;

/* Añadir el nuevo rol para usuarios */
ALTER TABLE Users MODIFY rol VARCHAR(10) DEFAULT 'normal' CHECK (rol IN ('root', 'normal', 'solicita'));
ALTER TABLE Users MODIFY rol ENUM('root', 'normal', 'solicita') DEFAULT 'normal';

/* Modificar el campo "rating" en las tablas ya existentes */
ALTER TABLE Movies MODIFY rating FLOAT;
ALTER TABLE Series MODIFY rating FLOAT;


---------------------------------------------------------------------------------------------
